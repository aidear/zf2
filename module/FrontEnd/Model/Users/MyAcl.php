<?php
/**
 * 自定义ACL
 *
 * @namespace BackEnd\Model\Users
 * @author Yaron Jiang
 * @version $id
 */

namespace BackEnd\Model\Users;
use Zend\Db\TableGateway\TableGateway;

use Zend\Cache\StorageFactory;

// use Zend\Permissions\Acl\Acl;
use Zend\Db\ResultSet\ResultSet;
use BackEnd\Model\Users\Role;
use BackEnd\Model\Users\RoleTable;
use BackEnd\Model\Users\Acl as DbAcl;
use BackEnd\Model\Users\AclTable;
use BackEnd\Model\Users\Resource;
use BackEnd\Model\Users\ResourceTable;
use Zend\Db\Adapter\Adapter;
use Zend\Cache\Storage\Adapter\AbstractAdapter;

class MyAcl
{
    public $acl;
    private $adapter;
    private $cache;
    
    const CACHENAME = 'backend_acl';
    
    function __construct(Adapter $adapter , AbstractAdapter $cache){
        $this->adapter = $adapter;
        $cache->addPlugin(new \Zend\Cache\Storage\Plugin\Serializer());
        $this->cache = $cache;
        $config = $this->cache->getOptions();
        $config->namespace = 'acl';
        $config->dir_permission = 0755;
        $this->cache->setOptions($config);
       
        $this->acl = $this->_getAcl();
    }
    

    //保存ACL到CACHE
    function saveAcl(){
        $this->cache->setItem(self::CACHENAME, $this->acl);
    }
    
    //获取ACL
    private function _getAcl(){
        if(!$this->acl){
            $this->_getAclCache();
        }
        if(!$this->acl){
            $this->_getAclDb();
        }
        
        $this->saveAcl();
        
        return $this->acl;
    }

    private function _getAclCache(){
        if($this->cache->hasItem(self::CACHENAME)){
            return $this->cache->getItem(self::CACHENAME);
        }
        
        return FALSE;
    }
    
    private function _getAclDb(){
        $this->acl = new \Zend\Permissions\Acl\Acl();

        $this->_insertRoles();
        $this->_insertResources();
        $this->_insertAllows();
        
    }
    
    private function _insertRoles(){
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Role());
        $roleTable = new RoleTable('sys_role' , $this->adapter , null , $resultSetPrototype);
        
        $roles = $roleTable->getAll();
        foreach($roles as $v){
            if($v->ParentName){
                $this->acl->addRole($v , $v->ParentName);
            }else{
                $this->acl->addRole($v);
            }
        }
        
    }
    
    private function _insertResources(){
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Resource());
        $table = new ResourceTable('sys_resource', $this->adapter , null , $resultSetPrototype);
        
        $resource = $table->getAll();
        foreach($resource as $v){
            $this->acl->addResource($v);
        }
    }
    
    private function _insertAllows(){
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new DbAcl());
        $table = new AclTable('sys_acl', $this->adapter ,  null , $resultSetPrototype);
        
        $acl = $table->getAll();
        
        foreach($acl as $v){
            $this->acl->allow($v->RoleName , $v->ResourceName);
        }
    }
}