<?php 
namespace FrontEnd\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
class FeedbackForm extends Form
{
    protected $inputFilter;

    public function __construct(){
        parent::__construct('feedback-form');
        $this->setAttribute('method', 'post');
        $this->add(array(
                'name' => 'content',
                'options' => array(
                        'label' => '反馈内容'
                ),
                'attributes' => array(
                    'id' => 'textarea2',
                    'rows' => 5,
                    'cols' => 50,
                    'data-trim' => 'true',
                    'data-required' => "true",
                    'data-describedby' => "for_content",
                    'data-description' => "content"
                ),
        ));

        $this->add(array(
                'name' => 'contact',
                'value' => '请优先填写QQ 或 Email',
                'options' => array(
                        'label' => '联系方式'
                ),
                'attributes' => array(
                        'id' => 'textfield33',
                        'data-trim' => 'true',
                        'data-required' => "true",
                        'data-describedby' => "for_contact",
                        'data-description' => "contact"
                ),
        ));
        $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                        'value' => '提交',
                        'type' => 'submit',
                        'id' => 'btn6',
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