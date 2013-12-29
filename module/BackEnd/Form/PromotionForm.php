<?php 
/**
 * PromotionForm.php
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
 * @version CVS: Id: PromotionForm.php,v 1.0 2013-12-26 下午7:58:36 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
class PromotionForm extends Form
{
	protected $inputFilter;
	function __construct() {
		parent::__construct ( 'promotion-form' );
		$this->setAttribute ( 'method', 'post' );
		$this->setAttribute ( 'enctype', 'multipart/form-data' );

		$this->add ( array (
				'name' => 'id',
				'options' => array (
						'label' => 'ID'
				)
		) );

		$this->add ( array (
				'name' => 'rule_code',
				'attributes' => array(
						'required' => 'required',
				),
				'type' => '\Zend\Form\Element\Select',
				'options' => array (
						'label' => '活动类型',
				)
		) );

		$this->add ( array (
				'name' => 'points',
				'options' => array (
						'label' => '积分基数',
				),
				'attributes' => array(
						'required' => 'required',
						'pattern' => '^[0-9]+(\.[0-9]{1,2})?$',
				)
		) );

		$this->add ( array (
				'name' => 'start_time',
				'options' => array (
						'label' => '开始时间',
				)
		) );
		$this->add ( array (
				'name' => 'end_time',
				'options' => array (
						'label' => '结束时间'
				)
		) );
		$this->add ( array (
				'name' => 'is_active',
				'type' => '\Zend\Form\Element\Select',
				'options' => array (
						'label' => '是否启用',
						'value_options' => array('1'=>'是', '0'=>'否'),
				)
		) );
		$this->add(array(
				'name' => 'last_update',
				'options' => array(
						'label' => '最后更新时间',
				)
		));
		$this->add(array(
				'name' => 'updateUser',
				'options' => array(
						'label' => '更新人',
				)
		));
		$this->add ( array (
				'name' => 'submit',
				'attributes' => array (
						'value' => '提交',
						'type' => 'submit',
				)
		) );
		
		$this->setInputFilter($this->getInputFilter());
	}
	public function getInputFilter(){
		if(!$this->inputFilter){
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
					'name' => 'points',
					'required' => true,
					'validators' => array(
							array('name' => 'StringLength' , 'options' => array('min' => 1 , 'max' => 11)),
							array('name' => 'Regex', 'options' => 
									array('pattern' => '/^[0-9]+(\.[0-9]{1,2})?$/',
											'messages' => array(
													'regexNotMatch' => '积分格式不正确'
											),
									)),
					),
			)));
			$inputFilter->add($factory->createInput(array(
					'name' => 'start_time',
					'required' => true
			)));
			$inputFilter->add($factory->createInput(array(
					'name' => 'end_time',
					'required' => true
			)));
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
}
?>