<?php
/**
 * Member.php
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
 * @version CVS: Id: Member.php,v 1.0 2013-9-18 下午11:06:54 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Users;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Member implements InputFilterAwareInterface
{
	public $UserID;
	public $UserName;
	public $Password;
	public $Nick;
	public $ImgUrl;
	public $Email;
	public $Mobile;
	public $Points;
	public $TrueName;
	public $Gender;
	public $Province;
	public $City;
	public $District;
	public $Address;
	public $Tel;
	public $Birthday;
	public $QQ;
	public $MSN;
	public $address_id;
	public $leftMsg;
	public $isValidEmail;
	public $isValidMobile;
	public $loginProtect;
	public $passwordStrong;
	public $LoginCount = 0;
	public $Status = 1;
	public $AddTime;
	public $Source = 1;
	public $LastLogin;
	public $LastUpdate;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->UserID = isset($data['UserID']) ? $data['UserID'] : '';
		$this->UserName = isset($data['UserName']) ? $data['UserName'] : '';
		$this->Password = isset($data['Password']) ? $data['Password'] : '';
		$this->Nick = isset($data['Nick']) ? $data['Nick'] : '';
		$this->ImgUrl = isset($data['ImgUrl']) ? $data['ImgUrl'] : '';
		$this->Email = isset($data['Email']) ? $data['Email'] : '';
		$this->Mobile = isset($data['Mobile']) ? $data['Mobile'] : '';
		$this->Points = isset($data['Points']) ? $data['Points'] : '0';
		$this->TrueName = isset($data['TrueName']) ? $data['TrueName'] : '';
		$this->Gender = isset($data['Gender']) ? $data['Gender'] : 0;
		$this->Province = isset($data['Province']) ? $data['Province'] : '';
		$this->City = isset($data['City']) ? $data['City'] : '';
		$this->District = isset($data['District']) ? $data['District'] : '';
		$this->Address = isset($data['Address']) ? $data['Address'] : '';
		$this->Tel = isset($data['Tel']) ? $data['Tel'] : '';
		$this->Birthday = isset($data['Birthday']) ? $data['Birthday'] : '';
		$this->QQ = isset($data['QQ']) ? $data['QQ'] : '';
		$this->MSN = isset($data['MSN']) ? $data['MSN'] : '';
		$this->address_id = isset($data['address_id']) ? $data['address_id'] : '';
		$this->leftMsg = isset($data['leftMsg']) ? $data['leftMsg'] : '';
		$this->isValidEmail = isset($data['isValidEmail']) ? $data['isValidEmail'] : 0;
		$this->isValidMobile = isset($data['isValidMobile']) ? $data['isValidMobile'] : 0;
		$this->loginProtect = isset($data['loginProtect']) ? $data['loginProtect'] : 0;
		$this->LoginCount = isset($data['LoginCount']) ? $data['LoginCount'] : 0;
		$this->passwordStrong = isset($data['passwordStrong']) ? $data['passwordStrong'] : 0;
		$this->Status = isset($data['Status']) ? $data['Status'] : 1;
		$this->AddTime = isset($data['AddTime']) ? $data['AddTime'] : date('Y-m-d H:i:s');
		$this->Source = isset($data['Source']) ? $data['Source'] : 1;
		$this->LastLogin = isset($data['LastLogin']) ? $data['LastLogin'] : date('Y-m-d H:i:s');
		$this->LastUpdate = isset($data['LastUpdate']) ? $data['LastUpdate'] : date('Y-m-d H:i:s');
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