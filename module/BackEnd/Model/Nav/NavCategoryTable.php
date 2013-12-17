<?php
/**
 * NavCategoryTable.php
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
 * @version CVS: Id: NavCategoryTable.php,v 1.0 2013-10-3 下午9:44:39 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\Nav;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\Nav\NavCategory;
use Zend\Db\Sql\Expression;

class NavCategoryTable extends TableGateway
{
    protected $table = "nav_category";
    protected $select;
    
    function getAllToPage(){
        $select = $this->getSql()->select()->order('order '.__LIST_ORDER);
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function getAll(){
        return $this->select()->order('order '.__LIST_ORDER);
    }
    
    function getlist($where = array(), $order = "order {__LIST_ORDER}")
    {
    	$select = $this->getSql()->select();
    	
    	if ($where) {
    		$select->where($where);
    	}
    	$select->order($order);
    	$resultSet = $this->selectWith($select);//echo str_replace("\"", "", $select->getSqlString()); exit;
    	return $resultSet->toArray();
    }
    
    function getOneById($id){
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    function getCateNameById($id)
    {
    	if (0 == $id) {
    		return '顶级分类';
    	}
    	$cate = $this->getOneById($id);
    	
    	return isset($cate->name) ? $cate->name : '';
    }
    function getPathByParent($pid)
    {
    	if (!$pid) return '';
    	$rowset = $this->select(array('id' => $pid));
    	$row = $rowset->current();
    	return rtrim($row->catPath, ',') . ',' . $pid . ',';
    }
    
    function getByName($name){
        $select = $this->getSql()->select();
        $where = function (Where $where) use($name){
            $where->like('name' , "$name%");
        };
        $select->where($where);
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function delete($id){
        return parent::delete(array("id" => $id));
    }
    function deleteCateTree($id)
    {
    	$where = "id={$id} OR INSTR(catPath, ',{$id},')";
    	return parent::delete($where);
    }
    function getCateTreeIds($id) {
    	$data = array();
    	$where = "id={$id} OR INSTR(catPath, ',{$id},')";
    	$select = $this->getSql()->select();
    	$select->columns(array('id'));
    	$select->where($where);
    	$rs = $this->selectWith($select);//echo str_replace("\"", "", $select->getSqlString()); exit;
    	if ($rs) {
    		foreach ($rs as $item) {
    			$data[] = $item->id;
    		}
    	}
    	return $data;
    }
	function save(NavCategory $navCategory){
        $navCategory = $navCategory->toArray();
        unset($navCategory['inputFilter']);
        unset($navCategory['imgUrl']);
        if(empty($navCategory['id'])){
            $rowset = $this->select(array('name' => $navCategory['name']));
            if($rowset->count() < 1){
                if ($this->insert($navCategory)) {
                	return $this->getLastInsertValue();
                }
            }
            return;
        }
        if(empty($navCategory['id'])){
            unset($navCategory['id']);
            if ($this->insert($navCategory)) {
            	return $this->getLastInsertValue();
            }
        }else{
            $id = $navCategory['id'];
            if($this->getOneById($id)){
                unset($navCategory['id']);
                unset($navCategory['addTime']);
                $this->update($navCategory , array('id' => $id));
            }else{
                throw new \Exception('Not find this navCategory:' . $id);
            }
        }
    }
    function updateFieldsByID($fields, $id)
    {
    	return $this->update($fields, array('UserID' => $id));
    }
    public function updateImage($id , $imageFile){
    	return $this->update(array('imgUrl' => $imageFile) , array('id' => (int)$id));
    }
    
    public function checkExist($attr, $UserID = 0)
    {
    	$select = $this->getSql()->select();
    	$select->where($attr);
    
    	if ($UserID) {
    		$select->where("UserID <> {$UserID}");
    	}//echo str_replace('"', '', $select->getSqlString());die;
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
    public function checkNavCanDel($id)
    {
    	$select = $this->getSql()->select();
//     	$where = function (Where $where) use($id){
//     		$where->like('name' , "$name%");
//     	};
		
		$sql = "SELECT COUNT(link.id) as total FROM link WHERE link.category IN (SELECT id FROM nav_category WHERE id={$id} OR INSTR(catPath, ',{$id},'))";
		$statement = $this->adapter->query($sql);
		$results = $statement->execute();
		$row = $results->current();
		return $row['total'];
    }
    
    function formatWhere(array $data){
    	$where = $this->_getSelect()->where;
    	if(!empty($data['name'])){
//     		if('id' == $data['searchType']){
//     			$where->equalTo('MerchantFeedConfig.MerchantID', (int)$data['search']);
//     		}elseif('name' == $data['searchType']){
    			$where->like('name', '%' . $data['name'] . '%');
//     		}
    	}
    	if (isset($data['root']) && $data['root'] == 1) {
//     		$where->equalTo('nav_category.parentID', '0');
			$this->select->where("nav_category.parentID=0");
    	} else {
//     		$where->notEqualTo('nav_category.parentID', '0');
    		$this->select->where("nav_category.parentID<>0");
    	}    
    	$this->select->where($where);
    	return $this;
    }
    public function getListToPaginator($order = array())
    {
    	$select = $this->_getSelect();
//     	$select->columns(array('id', 'name', 'subCount' => new Expression('COUNT(nav_category.id)')));
//     	$select->join(array('nc'=>'nav_category'), "INSTR(nc.catPath,CONCAT(',',nav_category.id,','))", array('catPath'), 'left');
    	if (!empty($order)) {
    		$select->order($order);
    	}
//     	$select->group(array("nav_category.id"));
//     	echo str_replace('"', '', $select->getSqlString());die;
    	 
    	return new DbSelect($select, $this->getAdapter());
    }
    public function getListsToPaginator(array $data, $order = array())
    {
    	$sql = "SELECT nt.*,(SUM(IFNULL(ns.lkCount, 0))+nt.lkCount) AS subLinkCount FROM
(SELECT nStat.id,nStat.name,nStat.line,nStat.subCount,nStat.parentID,nStat.order,nStat.isShow,nStat.updateTime,nStat.updateUser,nl.category,nl.catPath,nl.lkCount FROM
(SELECT na.id,na.name,na.line,na.parentID,na.isShow,na.updateTime,na.updateUser,COUNT(nc.id) AS subCount,na.order
FROM nav_category na
LEFT JOIN
nav_category nc
ON INSTR(nc.catPath,CONCAT(',',na.id,','))
GROUP BY na.id) AS nStat
LEFT JOIN
(SELECT nc.id,nc.name,nc.`parentID`,COUNT(lk.id) AS lkCount,lk.`title`,lk.category,nc.catPath
FROM nav_category nc
LEFT JOIN
link lk
ON lk.`category`=nc.`id`
GROUP BY nc.`id`) AS nl
ON nStat.id=nl.id) AS nt
LEFT JOIN
(SELECT nStat.id,nStat.name,nStat.subCount,nl.category,nl.catPath,nl.lkCount FROM
(SELECT na.id,na.name,na.parentID,COUNT(nc.id) AS subCount
FROM nav_category na
LEFT JOIN
nav_category nc
ON INSTR(nc.catPath,CONCAT(',',na.id,','))
GROUP BY na.id) AS nStat
LEFT JOIN
(SELECT nc.id,nc.name,nc.`parentID`,COUNT(lk.id) AS lkCount,lk.`title`,lk.category,nc.catPath
FROM nav_category nc
LEFT JOIN
link lk
ON lk.`category`=nc.`id`
GROUP BY nc.`id`) AS nl
ON nStat.id=nl.id) AS ns
ON INSTR(ns.catPath, CONCAT(',',nt.id,','))";
    	$where = '1=1';
    	if(!empty($data['name'])){
    		$where .= " AND nt.name LIKE '%{$data['name']}%'";
    	}
    	if (isset($data['root']) && $data['root'] == 1) {
    		$where .= " AND nt.parentID=0";
    	} else {
    		$where .= " AND nt.parentID<>0";
    	}
    	$sql .= " WHERE {$where} GROUP BY nt.id";
    	if (!empty($order)) {
    		list($k, $v) = each($order);
    		if (in_array($k, array('subLinkCount'))) {
    			$sql .= " ORDER BY {$k} {$v}";
    		} else {
    			$sql .= " ORDER BY nt.{$k} {$v}";
    		}
    	}
    	$rs = $this->getAdapter()->query($sql, Adapter::QUERY_MODE_EXECUTE);
    	return $rs ? $rs->toArray() : array();
    }
    protected function _getSelect(){
    	if(!isset($this->select)){
    		$this->select = $this->getSql()->select();
    	}
    	return $this->select;
    }
    public function getSubCountByPID($pid) {
    	$select = $this->getSql()->select();
    	$select->where("INSTR(catPath,CONCAT(',',{$pid},','))");
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
}