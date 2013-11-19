<?php
/**
 * NavCategory.php
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
 * @version CVS: Id: NavCategory.php,v 1.0 2013-10-3 下午9:39:35 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Nav;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class NavCategory implements InputFilterAwareInterface
{
	public $id;
	public $name;
	public $imgUrl;
	public $desc;
	public $keyword;
	public $catPath;
	public $parentID = 0;
	public $line = 2;
	public $isShow = 1;
	public $order = 1;
	public $addTime;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->name = isset($data['name']) ? $data['name'] : '';
		$this->imgUrl = isset($data['imgUrl']) ? $data['imgUrl'] : '';
		$this->desc = isset($data['desc']) ? $data['desc'] : '';
		$this->keyword = isset($data['keyword']) ? $data['keyword'] : '';
		$this->catPath = isset($data['catPath']) ? $data['catPath'] : '';
		$this->parentID = isset($data['parentID']) ? $data['parentID'] : 0;
		$this->line = isset($data['line']) ? $data['line'] : 2;
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
					'name'     => 'name',
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