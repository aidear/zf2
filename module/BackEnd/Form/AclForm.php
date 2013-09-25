<?php
namespace BackEnd\Form;

use Zend\Form\Form;

class AclForm extends Form
{
    function __construct(){
        parent::__construct('acl-form');
        
        $this->add(array(
            'name' => 'role',
        ));
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Select',
            'name' => 'resources',
            'options' => array(
                'label' => '选择要添加的资源',
            ),
            
            'attributes' => array(
                'multiple' => 'multiple',
            )
        ));
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Select',
            'name' => 'deresources',
            'options' => array(
                'label' => '选择要删除的资源'
            ),
            
            'attributes' => array(
                'multiple' => 'multiple',
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
        	'attributes' => array(
        		'value' => '提交',
            ),
        ));
    }
    
    function setResource($resources){
        if(empty($resources)){
            return $this;
        }
        if(!is_array($resources)){
            throw new \Exception('Resource是数组');
        }
        
        $values = array();
        foreach($resources as $v){
            $values[] = array('value' => $v , 'label' => $v);
        }
        
        $this->get('resources')->setValueOptions($values);
        $this->get('resources')->setAttribute('size', 5);
        return $this;
    }
    
    function setDeResource($resources){
        if(empty($resources)){
            return $this;
        }
        if(!is_array($resources)){
            throw new \Exception('Resource是数组');
        }
        
        $values = array();
        foreach($resources as $v){
            $values[] = array('value' => $v , 'label' => $v);
        }
        $this->get('deresources')->setValueOptions($values);
        $this->get('deresources')->setAttribute('size', 5);
        return $this;
    }
}