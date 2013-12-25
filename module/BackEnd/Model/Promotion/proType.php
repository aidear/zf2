<?php
/**
 * proType.php
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
 * @version CVS: Id: proType.php,v 1.0 2013-12-25 下午9:48:25 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\Promotion;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ProType implements InputFilterAwareInterface
{
	public $type_code;
	public $type_name;
	public $enable;
	public $add_time;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->type_code = isset($data['type_code']) ? $data['type_code'] : '';
		$this->type_name = isset($data['type_name']) ? $data['type_name'] : '';
		$this->enable = isset($data['enable']) ? $data['enable'] : 1;
		$this->add_time = isset($data['add_time']) ? $data['add_time'] : '';
	}
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}

	function toArray(){
		return get_object_vars($this);
	}
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory     = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
					'name'     => 'type_code',
					'required' => true,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 1,
											'max'      => 100,
									),
							),
					),
			)));
	/*
			$inputFilter->add($factory->createInput(array(
					'name'     => 'artist',
					'required' => true,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 1,
											'max'      => 100,
									),
							),
					),
			)));
	
			$inputFilter->add($factory->createInput(array(
					'name'     => 'title',
					'required' => true,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 1,
											'max'      => 100,
									),
							),
					),
			)));
	*/
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
}