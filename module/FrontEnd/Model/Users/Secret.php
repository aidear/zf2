<?php
/**
 * Secret.php
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
 * @version CVS: Id: Secret.php,v 1.0 2013-11-1 下午7:50:47 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Users;

class Secret
{

	public $id;
	public $isSelect;
	public $content;
	public $user_id;
	public $addTime;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->isSelect = isset($data['isSelect']) ? $data['isSelect'] : 1;
		$this->content = isset($data['content']) ? $data['content'] : '';
		$this->user_id = isset($data['user_id']) ? $data['user_id'] : '';
		$this->addTime = isset($data['addTime']) ? $data['addTime'] : date('Y-m-d H:i:s');
	}
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}

	function toArray(){
		return get_object_vars($this);
	}
}