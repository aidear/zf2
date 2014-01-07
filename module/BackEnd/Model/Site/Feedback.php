<?php
namespace BackEnd\Model\Site;

use Custom\Util\Utilities;
class Feedback
{
	public $id;
	public $content;
	public $contact;
	public $ip;
	public $add_time;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->content = isset($data['content']) ? $data['content'] : '';
		$this->contact = isset($data['contact']) ? $data['contact'] : '';
		$this->ip = isset($data['ip']) ? $data['ip'] : Utilities::onlineIP();
		$this->add_time = isset($data['add_time']) ? $data['add_time'] : date('Y-m-d H:i:s');
	}
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}

	function toArray(){
		return get_object_vars($this);
	}
}