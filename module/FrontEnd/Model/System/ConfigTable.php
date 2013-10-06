<?php
/**
 * ConfigTable.php
 *------------------------------------------------------
 *
 * 
 *
 * PHP versions 5
 *
 *
 *
 * @author Willing Peng<pcq2006@gmail.com>
 * @copyright (C) 2013-2018 
 * @version CVS: Id: ConfigTable.php,v 1.0 2013-9-21 下午9:51:27 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\System;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class ConfigTable extends TableGateway
{
    protected $table = "sys_config";
    
    public function getCate()
    {
    	$select = $this->getSql()->select();
    	$select->where(array('PID' => 0));
    	$select->order('sortOrder ASC');
    	$resultSet = $this->selectWith($select);
    	
    	$rs =  $resultSet->toArray();
    	$return = array();
    	foreach ($rs as $v) {
    		$return[$v['ID']] = $v['cName'];
    	}
    	
    	return $return;
    }
    
    public static function getSysConf($key)
    {
    	$sys_config = array();
    	if (file_exists('./data/sys_config.php')) {
    		$sys_config = include'./data/sys_config.php';
    	}
    	if (isset($sys_config[$key])) {
    		return $sys_config[$key];
    	}
    	$select = $this->getSql()->select();
    	$select->where(array('cKey' => $key));
    	$select->columns(array('cValue'));
    	$resultSet = $this->selectWith($select);
    	$rs = $resultSet->toArray();
    	
    	return isset($rs['cValue']) ? $rs['cValue'] : '';
    }
    
    public function setConfigValue($cKey, $value)
    {
    	return $this->update(array('cValue' => $value), array('cKey' =>$cKey));
    }
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