<?php
/**
 * RegionTable.php
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
 * @version CVS: Id: RegionTable.php,v 1.0 2013-9-20 上午10:27:48 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Users;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use FrontEnd\Model\Users\Region;

class RegionTable extends TableGateway
{
	protected $table = "region";
	
	public function getRegionByType($type = 1, $pid = 1)
	{
		$select = $this->getSql()->select();
		$select->where(array('region.region_type' => $type));
		if ($pid >= 0) {
			$select->where(array('region.parent_id' => $pid));
		}
		$resultSet = $this->selectWith($select);//echo str_replace('"', '', $select->getSqlString());die;
		
		return $resultSet->toArray();
	}
	public function getRidByName($name)
	{
		$select = $this->getSql()->select();
		$select->columns(array('region_id'));
		$select->where(array('region_name' => $name));
		$resultSet = $this->selectWith($select);//echo str_replace('"', '', $select->getSqlString());die;
		$rs = $resultSet->current();
		return isset($rs->region_id) ? $rs->region_id : '';
	}
	public function getSelectRegion($type = 1, $pid = 1) 
	{
		$result = array();
		$region = $this->getRegionByType($type, $pid);
		if ($region) {
			foreach ($region as $k => $v) {
				$result[$v['region_id']] = $v['region_name'];
			}
		}
		
		return $result;
	}
	
	public function getRegionByPid($pid = 0)
	{
		$result = array();
		$select = $this->getSql()->select();
		$select->where(array('region.parent_id' => $pid));
		$resultSet = $this->selectWith($select);//echo str_replace('"', '', $select->getSqlString());die;
		
		$region =  $resultSet->toArray();
		foreach ($region as $k => $v) {
			$result[] = array('region_id' => $v['region_id'], 'region_name' => $v['region_name']);
		}
		
		return $result;
	}
	public function getReginNameByID($id)
	{
		$rs = $this->select(array('region_id' => $id));
		$data = $rs->toArray();
		return isset($data['region_name']) ? $data['region_name'] : null;
	}
	public function getAreaNameByIDs($prov, $city, $dist)
	{
		$select = $this->getSql()->select();
		$select->where("region_id IN ('{$prov}', '{$city}', '{$dist}')");
		$select->order('region_type ASC');
		$arr = $this->selectWith($select)->toArray();//echo str_replace('"', '', $select->getSqlString());die;
		$data = array('address' => '', 'rs' => array());
		if ($arr) {
			foreach ($arr as $k=>$v) {
				$data['address'] .= " {$v['region_name']}";
				$data['rs'][$k] = $v['region_name'];
			}
			$data['addresss'] = trim($data['address']);
		}
		return $data;
	}
}