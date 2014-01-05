<?php 
namespace FrontEnd\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
class AdvApplyForm extends Form
{
    protected $inputFilter;

    public function __construct(){
        parent::__construct('adv-Apply-form');
        $this->setAttribute('method', 'post');
        $this->add(array(
                'name' => 'title',
                'options' => array(
                        'label' => '网站名称'
                ),
                'attributes' => array(
                    'id' => 'textfield2',
                    'size' => 45,
                    'data-trim' => 'true',
                    'data-required' => "true",
                    'data-describedby' => "for_title",
                    'data-description' => "title"
                ),
        ));

        $this->add(array(
                'name' => 'url',
                'options' => array(
                        'label' => '推广网址'
                ),
                'attributes' => array(
                        'id' => 'textfield',
                        'size' => 45,
                        'data-trim' => 'true',
                        'data-required' => "true",
                        'data-pattern' => "^[a-zA-z]+://[^\s]*$",
                        'data-describedby' => "for_url",
                        'data-description' => "url"
                ),
        ));
        $this->add(array(
                'name' => 'QQ',
                'options' => array(
                        'label' => 'QQ'
                ),
                'attributes' => array(
                        'id' => 'textfield8',
                        'size' => 45,
                        'data-pattern' => '^[1-9][0-9]{4,}$',
                        'data-describedby' => "for_QQ",
                        'data-description' => "QQ"
                ),
        ));
        $this->add(array(
                'name' => 'email',
                'options' => array(
                        'label' => 'E-mail'
                ),
                'attributes' => array(
                        'id' => 'textfield4',
                        'size' => 45,
                        'data-pattern' => '^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$',
                        'data-describedby' => "for_email",
                        'data-description' => "email"
                ),
        ));
        $this->add(array(
                'name' => 'tel',
                'options' => array(
                        'label' => '电话'
                ),
                'attributes' => array(
                        'id' => 'textfield3',
                        'size' => 45,
                ),
        ));
        $this->add(array(
                'name' => 'position',
                'options' => array(
                        'label' => '投放位置'
                ),
                'attributes' => array(
                        'id' => 'textfield5',
                        'size' => 45,
                ),
        ));
        $this->add(array(
                'name' => 'build_time',
                'options' => array(
                        'label' => '建站时间'
                ),
                'attributes' => array(
                        'id' => 'textfield6',
                        'size' => 45,
                ),
        ));
        $this->add(array(
                'name' => 'dailyView',
                'options' => array(
                        'label' => '日访问量'
                ),
                'attributes' => array(
                        'id' => 'textfield7',
                        'size' => 45,
                ),
        ));
        $this->add(array(
                'name' => 'summary',
                'options' => array(
                        'label' => '网站介绍'
                ),
                'attributes' => array(
                        'id' => 'textfield3',
                        'cols' => 60
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