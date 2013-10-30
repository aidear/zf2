<?php
/**
 * Address.php
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
 * @version CVS: Id: Address.php,v 1.0 2013-10-30 下午8:22:01 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Users;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Address implements InputFilterAwareInterface
{
	public $id;
	public $name;
	public $user_id;
	public $consignee;
	public $email;
	public $country;
	public $province;
	public $city;
	public $district;
	public $address;
	public $zipcode;
	public $tel;
	public $mobile;
	public $sign_building;
	public $bestTime;
	public $firstaddress;
	public $addTime;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->name = isset($data['name']) ? $data['name'] : '';
		$this->user_id = isset($data['user_id']) ? $data['user_id'] : '';
		$this->consignee = isset($data['consignee']) ? $data['consignee'] : '';
		$this->email = isset($data['email']) ? $data['email'] : '';
		$this->country = isset($data['country']) ? $data['country'] : 0;
		$this->province = isset($data['province']) ? $data['province'] : '';
		$this->city = isset($data['city']) ? $data['city'] : '';
		$this->district = isset($data['district']) ? $data['district'] : '';
		$this->address = isset($data['address']) ? $data['address'] : 0;
		$this->zipcode = isset($data['zipcode']) ? $data['zipcode'] : '';
		$this->tel = isset($data['tel']) ? $data['tel'] : '';
		$this->mobile = isset($data['mobile']) ? $data['mobile'] : '';
		$this->sign_building = isset($data['sign_building']) ? $data['sign_building'] : '';
		$this->bestTime = isset($data['bestTime']) ? $data['bestTime'] : '';
		$this->firstaddress = isset($data['firstaddress']) ? $data['firstaddress'] : '';
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
					'name'     => 'UserName',
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
											'min'      => 3,
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