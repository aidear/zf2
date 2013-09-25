<?php
namespace BackEnd\Model\Users;

use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;

class RoleTable extends TableGateway
{
    protected $table = 'Sys_Role';
    
    function getAll(){
        return $this->select();
    }
    
    function getOneForId($id){
        $id = (int)$id;
        $rowset = $this->select(array('RoleID' => $id));
        $row = $rowset->current();
        if(!$row){
            throw new \Exception('No find this Role.');
        }
        return $row;
    }
    
    function save(Role $role){
        $role = $role->toArray();
        if(empty($role['RoleID'])){
            unset($role['RoleID']);
            unset($role['inputFilter']);
            return $this->insert($role);
        }else{
            if($this->getOneForId($role['RoleID'])){
                $id = $role['RoleID'];
                unset($role['RoleID']);
                unset($role['inputFilter']);
                
                return $this->update($role , array('RoleID' => $id));
            }else{
                throw new \Exception('No find this Role');
            }
        }
    }
    function checkIsExist($name) {
    	return $this->select(array('Name' => $name))->count();
    }
    
    function deleteForName($name){
        $this->delete(array('Name' => $name));
    }
}