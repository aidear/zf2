<?php
/**
 * 资源
 * @author Yaron Jiang
 * @version $id
 */
namespace BackEnd\Model\Users;
use Zend\Stdlib\ArraySerializableInterface;

use Zend\Permissions\Acl\Resource\ResourceInterface;

class Resource implements ResourceInterface , ArraySerializableInterface
{
    public $ResourceID;
    public $Name;
    public $Controller;
    public $ControllerName;
    public $Action;
    public $ActionName;
    public $AddTime;
    
    function __construct($name = NULL , $controller = NULL , $action = NULL){
        $this->Name = $name;
        $this->Controller = $controller;
        $this->Action = $action;
    }
    
    function getResourceId(){
        return $this->Name;
    }
    
    function __toString(){
        return $this->Name;
    }
    
    function exchangeArray(Array $data){
        $this->ResourceID = isset($data['ResourceID'])?$data['ResourceID']:'';
        $this->Name = isset($data['Name'])?$data['Name']:'';
        $this->Controller = isset($data['Controller'])?$data['Controller']:'';
        $this->ControllerName = isset($data['ControllerName'])?$data['ControllerName']:'';
        $this->Action = isset($data['Action'])?$data['Action']:'';
        $this->ActionName = isset($data['ActionName'])?$data['ActionName']:'';
        $this->AddTime = isset($data['AddTime'])?$data['AddTime']:'';
        $this->State = isset($data['State'])?$data['State']:'';
    }
    
    function getArrayCopy(){
        $params = get_object_vars($this);
        foreach($params as $k => $v){
            if(empty($v)){
                unset($params[$k]);
            }
        }
        
        return $params;
    }
    
    function toArray(){
        return $this->getArrayCopy();
    }
}