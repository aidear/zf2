<?php
namespace FrontEnd\Model\Nav;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RecommendLink implements InputFilterAwareInterface
{
	public $id;
	public $title;
	public $url;
	public $category;
	public $user_name;
	public $email;
	public $mobile;
	public $description;
	public $note;
	public $status;
	public $approvedTime;
	public $approvedUser;
	public $addTime;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->title = isset($data['title']) ? $data['title'] : '';
		$this->url = isset($data['url']) ? $data['url'] : '';
		$this->category = isset($data['category']) ? $data['category'] : 0;
		$this->user_name = isset($data['user_name']) ? $data['user_name'] : NULL;
		$this->email = isset($data['email']) ? $data['email'] : NULL;
		$this->mobile = isset($data['mobile']) ? $data['mobile'] : '';
		$this->description = isset($data['description']) ? $data['description'] : '';
		$this->note = isset($data['note']) ? $data['note'] : '';
		$this->status = isset($data['status']) ? $data['status'] : 0;
		$this->approvedTime = isset($data['approvedTime']) ? $data['approvedTime'] : date('Y-m-d H:i:s');
		$this->approvedUser = isset($data['approvedUser']) ? $data['approvedUser'] : '';
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