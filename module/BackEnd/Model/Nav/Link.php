<?php
/**
 * Link.php
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
 * @version CVS: Id: Link.php,v 1.0 2013-10-4 下午3:01:01 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\Nav;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Link implements InputFilterAwareInterface
{
	public $id;
	public $title;
	public $url;
	public $target;
	public $category;
	public $province;
	public $city;
	public $isShow = 1;
	public $order = 1;
	public $addTime;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->title = isset($data['title']) ? $data['title'] : '';
		$this->url = isset($data['url']) ? $data['url'] : '';
		$this->target = isset($data['target']) ? $data['target'] : '';
		$this->category = isset($data['category']) ? $data['category'] : 0;
		$this->province = isset($data['province']) ? $data['province'] : NULL;
		$this->city = isset($data['city']) ? $data['city'] : NULL;
		$this->isShow = isset($data['isShow']) ? $data['isShow'] : 1;
		$this->order = isset($data['order']) ? $data['order'] : 1;
		$this->addTime = isset($data['addTime']) ? $data['addTime'] : date('Y-m-d H:i:s');
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