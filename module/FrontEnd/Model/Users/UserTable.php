<?php
namespace BackEnd\Model\Users;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\Users\User;

class UserTable extends TableGateway
{
    protected $table = "adminuser";
    
    function getUserByName($name)
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
        $rowset = $this->select(array('Mail' => $email));
        $row = $rowset->current();
        return $row;
    }
    
    function getOneForId($id){
        $rowset = $this->select(array('ID' => $id));
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
        return parent::delete(array("ID" => $id));
    }
    
    function save(User $user){
        $user = (array)$user;
        if(empty($user['ID'])){
            unset($user['ID']);
            $this->insert($user);
        }else{
            $id = $user['ID'];
            if($this->getOneForId($id)){
                unset($user['ID']);
                return $this->update($user , array('ID' => $id));
            }else{
                return false;
            }
        }
    }
}