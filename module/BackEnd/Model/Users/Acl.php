<?php
namespace BackEnd\Model\Users;

use Zend\Stdlib\ArraySerializableInterface;

class Acl implements ArraySerializableInterface
{
    public $AclID;
    public $RoleName;
    public $ResourceName;
    public $AddTime;
    
    function __construct(){
    }
    
    function exchangeArray(Array $data){
        $this->AclID = isset($data['AclID'])?$data['AclID']:'';
        $this->RoleName = isset($data['RoleName'])?$data['RoleName']:'';
        $this->ResourceName = isset($data['ResourceName'])?$data['ResourceName']:'';
        $this->AddTime = isset($data['AddTime'])?$data['AddTime']:'';
    }
    
    function getArrayCopy(){
        return get_object_vars($this);
    }
    function toArray() {
    	return get_object_vars ( $this );
    }
}