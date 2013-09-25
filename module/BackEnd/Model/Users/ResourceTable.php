<?php
namespace BackEnd\Model\Users;

use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;

class ResourceTable extends TableGateway
{
    protected $table = 'Sys_Resource';
    
    function getPage(){
        $select = $this->getSql()->select();
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function getAll(){
        return $this->select();
    }
    
    function getOneForId($id){
        $id = (int)$id;
        $rowset = $this->select(array('ResourceID' => $id));
        $row = $rowset->current();
        if(!$row){
            throw new \Exception('Not find this Resource.');
        }
        
        return $row;
    }
    
    function save(Resource $resource){
        $resource = $resource->toArray();
        if(empty($resource['ResourceID'])){
            $rowset = $this->select(array('Name' => $resource['Name']));
            if($rowset->count() < 1){
                $this->insert($resource);
            }
            return;
        }
        if(empty($resource['ResourceID'])){
            unset($resource['ResourceID']);
            $this->insert($resource);
        }else{
            $id = $resource['ResourceID'];
            if($this->getOneForId($id)){
                unset($resource['ResourceID']);
                $this->update($resource , array('ResourceID' => $id));
            }else{
                throw new \Exception('Not find this Resource:' . $id);
            }
        }
    }
    
    function removeForName($name){
        $this->delete(array('Name' => $name));
    }
}