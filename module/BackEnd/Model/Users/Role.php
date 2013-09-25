<?php
namespace BackEnd\Model\Users;

use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;

class Role implements RoleInterface, InputFilterAwareInterface
{
    public $Name;
    public $CnName;
    public $ParentName;
    public $AddTime;
    public $State;
    public $RoleID;
    
    private $inputFilter;
    function __construct($name = '', $parentName = '') {
        $this->Name = $name;
        $this->ParentName = $parentName;
        $this->State = '1';
    }
    function getRoleId() {
        return $this->Name;
    }
    function exchangeArray(Array $data) {
        $this->Name = isset ( $data ['Name'] ) ? $data ['Name'] : '';
        $this->CnName = isset ( $data ['CnName'] ) ? $data ['CnName'] : '';
        $this->ParentName = isset ( $data ['ParentName'] ) ? $data ['ParentName'] : '';
        $this->AddTime = isset ( $data ['AddTime'] ) ? $data ['AddTime'] : '';
        $this->State = isset ( $data ['State'] ) ? $data ['State'] : '';
        $this->RoleID = isset ( $data ['RoleID'] ) ? $data ['RoleID'] : '';
    }
    function toArray() {
        return get_object_vars ( $this );
    }
    function __toString() {
        return $this->Name;
    }
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    	throw new \Exception("Not used");
    }
    public function getInputFilter()
    {
    	if (!$this->inputFilter) {
    		$inputFilter = new InputFilter();
    		$factory = new InputFactory();
    		
    		$inputFilter->add($factory->createInput(array(
    			'name' => 'Name',
    			'required' => true,
    			'filters' => array(
    				array('name' => 'StripTags'),
    				array('name' => 'StringTrim'),
    			),
    			'validators' => array(
    				array('name' => 'StringLength',
    					  'options' => array(
    							'encoding' => 'UTF-8',
    					  		'min' => 1,
    					  		'max' => 100,
    						))
    			),
    		)));
    		$this->inputFilter = $inputFilter;
    	}
    	return $this->inputFilter;
    }
}