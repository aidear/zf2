<?php
namespace BackEnd\Form;

use Zend\Form\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    protected $inputFilter;
    
    public function __construct(){
        parent::__construct('login-form');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'username',
            'options' => array(
                'label' => '用户名'
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'options' => array(
                'label' => '密码'
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Login',
                'type' => 'submit',
            ),
        ));
        
        $this->setInputFilter($this->getInputFilter());
    }
    
    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'username',
                'required' => true,
                'validators' => array(
                    array('name' => 'StringLength' , 'options' => array('min' => 3 , 'max' => 20)),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'password',
                'required' => true,
                'validators' => array(
                    array('name' => 'Alnum'),
                ),
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}