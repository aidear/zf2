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

class MemberTable extends TableGateway
{
    protected $table = "member";
    protected $select;
    
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
    		$where->like('UserName', '%' . $data['k'] . '%')->orPredicate(new Like('Email', '%' . $data['k'] . '%'))
    		->orPredicate(new Like('Nick', '%' . $data['k'] . '%'))
    		->orPredicate(new Like('Mobile', '%' . $data['k'] . '%'));
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