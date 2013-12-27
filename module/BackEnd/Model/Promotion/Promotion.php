<?php
/**
 * promotion.php
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
 * @version CVS: Id: promotion.php,v 1.0 2013-12-25 下午9:44:09 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\Promotion;

class Promotion
{
	public $id;
	public $rule_code;
	public $points;
	public $start_time;
	public $end_time;
	public $is_active;
	public $add_time;
	public $last_update;
	public $updateUser;
	
	protected $inputFilter;


	function exchangeArray(Array $data){
		$this->id = isset($data['id']) ? $data['id'] : '';
		$this->rule_code = isset($data['rule_code']) ? $data['rule_code'] : '';
		$this->points = isset($data['points']) ? $data['points'] : '';
		$this->start_time = isset($data['start_time']) ? $data['start_time'] : '';
		$this->end_time = isset($data['end_time']) ? $data['end_time'] : '';
		$this->is_active = isset($data['is_active']) ? $data['is_active'] : 1;
		$this->add_time = isset($data['add_time']) ? $data['add_time'] : NULL;
		$this->last_update = isset($data['last_update']) ? $data['last_update'] : NULL;
		$this->updateUser = isset($data['updateUser']) ? $data['updateUser'] : NULL;
	}
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}

	function toArray(){
		return get_object_vars($this);
	}
}