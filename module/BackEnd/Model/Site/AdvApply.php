<?php
namespace BackEnd\Model\Site;

use Custom\Util\Utilities;
class AdvApply
{
	public $id;
	public $title;
	public $url;
	public $QQ;
	public $email;
	public $tel;
	public $position;
	public $build_time;
	public $dailyView;
	public $summary;
	public $ip;
	public $add_time;

	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->title = isset($data['title']) ? $data['title'] : '';
		$this->url = isset($data['url']) ? $data['url'] : '';
		$this->QQ = isset($data['QQ']) ? $data['QQ'] : '';
		$this->email = isset($data['email']) ? $data['email'] : '';
		$this->tel = isset($data['tel']) ? $data['tel'] : '';
		$this->position = isset($data['position']) ? $data['position'] : '';
		$this->build_time = isset($data['build_time']) ? $data['build_time'] : '';
		$this->dailyView = isset($data['dailyView']) ? $data['dailyView'] : 0;
		$this->summary = isset($data['summary']) ? $data['summary'] : '';
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