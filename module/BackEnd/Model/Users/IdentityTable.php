<?php
namespace BackEnd\Model\Users;

use Custom\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Like;

class IdentityTable extends TableGateway
{
    protected $table = "identity_record";
    protected $select;
    function getPage(){
    	$select = $this->getSql()->select();
    	$select->order("status ASC,addTime DESC");
    	$adapter = new DbSelect($select, $this->getAdapter());
    	return $adapter;
    }
    function updateFieldsByID($fields, $id)
    {	$filter =  get_object_vars(new Identity());
    	$f = array_keys($filter);
    	foreach ($fields as $k=>$v) {
    		if (!in_array($k, $f)) {
    			unset($fields[$k]);
    		}
    	}
    	return $this->update($fields, array('UserID' => $id));
    }
    function updateStatus($status = 1, $id)
    {
    	$update = array('status' => $status);
    	if (is_array($id)) {
			$where = $this->getSql()->select()->where;
			$where->in('id', $id);
    	} else {
    		$where = array('id' => $id);
    	}
    	if ($status == 1) {
    		$update['lastApproved'] = date('Y-m-d H:i:s');
    	}
    	return $this->update($update, $where);
    }
    public function checkExist($attr, $UserID = 0)
    {
    	$select = $this->getSql()->select();
    	$select->where($attr);
    
    	if ($UserID) {
    		$select->where("user_id <> {$UserID}");
    	}//echo str_replace('"', '', $select->getSqlString());die;
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
    function getOneForId($id){
    	if (is_array($id)) {
    		$select = $this->getSql()->select();
    		$where = function(Where $where) use ($id) {
    			$where->in('user_id', $id);
    		};
    		$select->where($where);
    		return $this->selectWith($select);
    	} else {
    		$rowset = $this->select(array('user_id' => $id));
    		$row = $rowset->current();
    		return $row;
    	}
    }
    function getOneByUID($id){
    	$rowset = $this->select(array('user_id' => $id));
    	$row = $rowset->current();
    	return $row;
    }
    function formatWhere(array $data){
        $where = $this->_getSelect()->where;
        if(!empty($data['k'])){
            $where->like('member.UserName', '%' . $data['k'] . '%')->orPredicate(new Like('name', '%' . $data['k'] . '%'))
            ->orPredicate(new Like('code', '%' . $data['k'] . '%'));
        }
        $this->select->join('member', 'identity_record.user_id=member.UserID', 'UserName');
        if (isset($data['Points'])) {
            $sep = explode('|', $data['Points']);
            $field = $sep[0];
            $than = $sep[1];
            $v = $sep[2];
            switch($than) {
            	case 'than':
            	    $where->greaterThan($field, $v);
            	    break;
            	case 'lthan':
            	    $where->lessThan($field, $v);
            	    break;
            	case 'nequal':
            	    $where->equalTo($field, $v);
            	    break;
            	case 'sthan':
            	    $where->lessThanOrEqualTo($field, $v);
            	    break;
            	case 'bthan':
            	    $where->bthan($field, $v);
            	    break;
            	default:
            	    break;
            }
        }
        if (isset($data['Gender'])) {
            $where->equalTo('Gender', $data['Gender']);
        }
        $this->select->where($where);
        return $this;
    }
    public function getListToPaginator($order = array())
    {
        $select = $this->_getSelect();
        if (!empty($order)) {
            $select->order($order);
        } else {
            $select->order(array('status' => 'DESC'));
        }//echo str_replace('"', '', $select->getSqlString());die;
    
        return new DbSelect($select, $this->getAdapter());
    }
    protected function _getSelect(){
        if(!isset($this->select)){
            $this->select = $this->getSql()->select();
        }
        return $this->select;
    }
}