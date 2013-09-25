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

class MemberTable extends TableGateway
{
    protected $table = "member";
    
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
        $rowset = $this->select(array('UserID' => $id));
        $row = $rowset->current();
        return $row;
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
    	unset($u['inputFilter']);
    	unset($u['Password']);
    	unset($u['ImgUrl']);
        if(empty($u['UserID'])){
            unset($u['ID']);
            $this->insert($u);
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
}