<?php
namespace BackEnd\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class RoleForm extends Form
{
	protected $inputFilter = null;
    function __construct(){
        parent::__construct('role-form');
        
        $this->add(array(
            'name' => 'RoleID',
            'attributes' => array(
                'value' => '',
            )
        ));
        
        $this->add(array(
            'name' => 'Name',
            'options' => array(
                'label' => '角色'
            )
        ));
        $this->add(array(
        	'name' => 'CnName',
        	'options' => array(
        		'label'	=> '角色别名'
        	)
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'ParentName',
            'options' => array(
                'label' => '父级角色'
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
        	'type' => 'Submit',
        	'attributes' => array(
//                 'id' => 'submitbutton',
        		'value' => '提交',
            ),
        ));
        $this->setInputFilter($this->getInputFilter());
    }
    
    function setParents($role , $selected = null){
        $roles = $role->toArray();
        if (empty ( $roles )) {
            return null;
        }
        
        $re = array (array('label' => '无' , 'value' => ''));
        foreach ( $roles as $role ) {
            $row = array (
                'value' => $role ['Name'],
                'label' => $role ['Name'] 
            );
            if ($selected == $role ['Name']) {
                $row ['selected'] = 'selected';
            }
            
            $re[] = $row;
        }
        
        $this->get( 'ParentName' )->setValueOptions ( $re );
    }
    public function getInputFilter(){
    	if(!$this->inputFilter){
    		$inputFilter = new InputFilter();
    		$factory = new InputFactory();
    
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'Name',
    				'required' => true,
    				'validators' => array(
    						array('name' => 'StringLength' , 'options' => array('min' => 3 , 'max' => 20)),
    				),
    		)));
    
//     		$inputFilter->add($factory->createInput(array(
//     				'name' => 'password',
//     				'required' => true,
//     				'validators' => array(
//     						array('name' => 'Alnum'),
//     				),
//     		)));
    
    		$this->inputFilter = $inputFilter;
    	}
    
    	return $this->inputFilter;
    }
}