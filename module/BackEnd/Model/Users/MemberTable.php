<?php
namespace BackEnd\Model\Users;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\Users\Member;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Predicate\Operator;

class MemberTable extends TableGateway
{
    protected $table = "member";
    protected $select;
    public function getStatistics()
    {
    	$sql = "SELECT SUM(IF (DATE_FORMAT(`AddTime`, '%Y-%m-%d')=CURDATE(), 1, 0)) AS todayTotal,
SUM(IF (WEEK(`AddTime`)=WEEK(NOW()), 1, 0)) AS weekTotal,
SUM(IF (DATE_FORMAT(`AddTime`, '%Y-%m')=DATE_FORMAT(NOW(), '%Y-%m'), 1, 0)) AS monthTotal,
SUM(IF (QUARTER(`AddTime`) = QUARTER(NOW()), 1, 0)) AS quarterTotal,
SUM(IF (YEAR(`AddTime`)=YEAR(NOW()), 1, 0)) AS yearTotal,
COUNT(1) as allTotal
FROM member;";
    	$rs = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
    	return $rs->current();
    }
    function getMemberByName($name)
    {
//     	$rowset = $this->select(array('Status' => 1, 'UserName' => $name));
//     	$row = $rowset->current();
//     	return $row;
		$select = $this->getSql()->select();
		$select->where(array('Status' => 1, 'UserName' => $name));//echo str_replace('"', '', $select->getSqlString());die;
		$resultSet = $this->selectWith($select);
		
		return $resultSet->current();
    }
    function getAllToPage(){
        $select = $this->getSql()->select();
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function getAll(){
        return $this->select();
    }
    
    function getOneForEmail($email){
        $rowset = $this->select(array('EMail' => $email));
        $row = $rowset->current();
        return $row;
    }
    
    function getOneForId($id){
    	if (is_array($id)) {
    		$select = $this->getSql()->select();
    		$where = function(Where $where) use ($id) {
    			$where->in('UserID', $id);
    		};
    		$select->where($where);
//     		echo str_replace('"', '', $select->getSqlString());die;
    		return $this->selectWith($select);
    	} else {
	        $rowset = $this->select(array('UserID' => $id));
	        $row = $rowset->current();
	        return $row;
    	}
    }
    function getUserNameByID($uid) {
    	$row = $this->getOneForId($uid);
    	return isset($row->UserName) ? $row->UserName : null;
    }
    function getUserListByID($id = array()) {
    	if (is_array($id)) {
    		$select = $this->getSql()->select();
    		$where = function(Where $where) use ($id) {
    			$where->in('UserID', $id);
    		};
    		$select->where($where);
    		return $this->selectWith($select);
    	} else {
    		return null;
    	}
    }
    
    function getUserForName($name){
        $select = $this->getSql()->select();
        $where = function (Where $where) use($name){
            $where->like('UserName' , "$name%");
        };
        $select->where($where);
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function delete($id){
        return parent::delete(array("UserID" => $id));
    }
    
    function save(Member $user){
    	$u = $user->toArray();
    	unset($u['dbAdapter']);
    	unset($u['inputFilter']);
    	unset($u['Password']);
    	unset($u['ImgUrl']);
        if(empty($u['UserID'])){
            unset($u['ID']);
            if ($this->insert($u)) {
            	return $this->getLastInsertValue();
            }
        }else{
            $id = $u['UserID'];
            if($this->getOneForId($id)){
                unset($u['UserID']);
                return $this->update($u , array('UserID' => $id));
            }else{
                return false;
            }
        }
    }
    function updateFieldsByID($fields, $id)
    {
    	return $this->update($fields, array('UserID' => $id));
    }
    public function updateImage($UserID , $imageFile){
    	return $this->update(array('ImgUrl' => $imageFile) , array('UserID' => (int)$UserID));
    }
    
    public function checkExist($attr, $UserID = 0)
    {
    	$select = $this->getSql()->select();
    	$select->where($attr);
    
    	if ($UserID) {
    		$select->where("UserID <> {$UserID}");
    	}//echo str_replace('"', '', $select->getSqlString());die;
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
    function formatWhere(array $data){
    	$where = $this->_getSelect()->where;
    	if(!empty($data['k'])){
//     		$where->like('UserName', '%' . $data['k'] . '%')->orPredicate(new Like('Email', '%' . $data['k'] . '%'))
//     		->orPredicate(new Like('Nick', '%' . $data['k'] . '%'))
//     		->orPredicate(new Like('Mobile', '%' . $data['k'] . '%'))
//     		->orPredicate(new Like('Address', '%' . $data['k'] . '%'));
			$wh = "(UserName Like '%{$data['k']}%' OR Email Like '%{$data['k']}%' OR Nick Like "
				." '%{$data['k']}%' OR Mobile Like '%{$data['k']}%' OR Address Like '%{$data['k']}%')";
    		$this->select->where($wh);
    	}
    	if (!empty($data['Province'])) {
    		$where->equalTo('Province', $data['Province']);
    	}
    	if (!empty($data['City'])) {
    		$where->equalTo('City', $data['City']);
    	}
    	if (!empty($data['District'])) {
    		$where->equalTo('District', $data['District']);
    	}
    	$this->select->where($where);
    	//filter
    	if (isset($data['filter'])) {
    		$filter = $data['filter'];
    		foreach ($filter['fields'] as $k=>$r) {
    			$opt = $filter['opts'][$k];
    			$val = $filter['vals'][$k];
    			$combine = 1;
    			if ($k != 0) {
    				$combine = $filter['rel'][$k-1];
    			}
    			switch ($opt) {
    				case 'than'://大于
    					switch($combine) {
    						case 2:
    							$where->orPredicate(new Operator($r, '>', $val));
    							break;
    						case 3:
    							$where->orPredicate(new Operator($r, '<=', $val));
    							break;
    						default:
    							$where->greaterThan($r, $val);
    							break;
    					}
    					break;
    				case 'lthan'://小于
    					switch($combine) {
    						case 2:
    							$where->orPredicate(new Operator($r, '<', $val));
    							break;
    						case 3:
    							$where->orPredicate(new Operator($r, '>=', $val));
    							break;
    						default:
    							$where->greaterThan($r, $val);
    							break;
    					}
    					break;
    				case 'sthan'://小于等于
    					switch($combine) {
    						case 2:
    							$where->orPredicate(new Operator($r, '<=', $val));
    							break;
    						case 3:
    							$where->orPredicate(new Operator($r, '>', $val));
    							break;
    						default:
    							$where->lessThanOrEqualTo($r, $val);
    							break;
    					}
    					break;
    				case 'bthan'://大于等于
    					switch($combine) {
    						case 2:
    							$where->orPredicate(new Operator($r, '>=', $val));
    							break;
    						case 3:
    							$where->orPredicate(new Operator($r, '<', $val));
    							break;
    						default:
    							$where->greaterThanOrEqualTo($r, $val);
    							break;
    					}
    					break;
    				case 'in':
    					if ($k != 0) {
    						
    					}
    					$where->like($r, "%".$val."%");
    					break;
    				case 'eq':
    				case 'nequal':
    					switch ($combine) {
    						case 2:
    							$where->orPredicate(new Operator($r, '=', $val));
    							break;
    						case 3:
    							$where->notEqualTo($r, $val);
    							break;
    						default:
    							$where->equalTo($r, $val);
    							break;
    					}
    					break;
    				default:
    					break;
    			}
    		}
    	}
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
    		$select->order(array('LastUpdate' => 'DESC'));
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