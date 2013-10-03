<?php
namespace FrontEnd\Model\Users;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use FrontEnd\Model\Users\Member;

class MemberTable extends TableGateway
{
    protected $table = "member";
    
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
    function getOneById($id){
    	$rowset = $this->select(array('UserID' => $id));
    	$row = $rowset->current();
    	return $row;
    }
    function getUserByUserName($userName)
    {
    	$rowset = $this->select("(UserName='{$userName}' OR Email='{$userName}') AND Status=1");
    	$row = $rowset->current();
    	return $row;
    }
}