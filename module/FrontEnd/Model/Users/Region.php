<?php
/**
 * Region.php
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
 * @version CVS: Id: Region.php,v 1.0 2013-9-20 上午10:27:43 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Users;

class Region
{
	public $region_id;
	public $parent_id;
	public $region_name;
	public $region_type;
	public $zip;

	function exchangeArray(Array $data){
		$this->region_id = isset($data['region_id']) ? $data['region_id'] : '';
		$this->parent_id = isset($data['parent_id']) ? $data['parent_id'] : '';
		$this->region_name = isset($data['region_name']) ? $data['region_name'] : '';
		$this->region_type = isset($data['region_type']) ? $data['region_type'] : '';
		$this->zip = isset($data['zip']) ? $data['zip'] : '';
	}

	function toArray(){
		return get_object_vars($this);
	}
}