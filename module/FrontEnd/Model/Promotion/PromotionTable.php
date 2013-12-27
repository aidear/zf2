<?php
/**
 * LinkTable.php
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
 * @version CVS: Id: LinkTable.php,v 1.0 2013-10-4 下午3:03:16 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Model\Promotion;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class PromotionTable extends TableGateway
{
    protected $table = "pro_rule";
    protected $select;
    
    function getAllToPage($where = array()){
        $select = $this->getSql()->select()->order('order Desc');
        if ($where) {
        	$select->where($where);
        }
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function getAll($where = array()){
    	if ($where) {
    		return  $this->select($where)->order('order Desc');
    	}
        return $this->select()->order('order Desc');
    }
    
    function getlist($where = array(), $order = "start_time ASC")
    {
    	$select = $this->getSql()->select();
    	
    	if ($where) {
    		$select->where($where);
    	}
    	$select->order($order);
    	$resultSet = $this->selectWith($select);//echo str_replace("\"", "", $select->getSqlString()); exit;
    	return $resultSet->toArray();
    }
    function getProList($code = NULL)
    {
        $now = date('Y-m-d H:i:s');
        $select = $this->getSql()->select();
        $where = $select->where;
        if ($code) {
            $where->equalTo('rule_code', $code);
        }
        $where->equalTo('is_active', 1);
        $where->lessThanOrEqualTo('start_time', $now);
        $where->greaterThanOrEqualTo('end_time', $now);
        $select->join('pro_rule_type', 'pro_rule_type.type_code=pro_rule.rule_code', array('type_name'));
        $select->where($where);
        $select->order("start_time ASC");
        $rs = $this->selectWith($select);
        
        return $rs->toArray();
    }
    function getOneById($id){
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    
    function getByName($name){
        $select = $this->getSql()->select();
        $where = function (Where $where) use($name){
            $where->like('title' , "$name%");
        };
        $select->where($where);
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function delete($id){
        return parent::delete(array("id" => $id));
    }
    function deleteMuti($where) {
    	return parent::delete($where);
    }
    function updateDelCateLink($cid = array())
    {
    	$where = $this->getSql()->select()->where;
    	$where->in('category', $cid);
    	return parent::update(array('category' => 0), $where);
    }
	function save(Promotion $promotion){
        $promotion = $promotion->toArray();
        unset($promotion['inputFilter']);
        if(empty($promotion['id'])){
                if ($this->insert($promotion)) {
                	return $this->getLastInsertValue();
                }
            return;
        }
        if(empty($promotion['id'])){
            unset($promotion['id']);
            if ($this->insert($promotion)) {
            	return $this->getLastInsertValue();
            }
        }else{
            $id = $promotion['id'];
            if($this->getOneById($id)){
                unset($promotion['id']);
                unset($promotion['add_time']);
                $this->update($promotion , array('id' => $id));
            }else{
                throw new \Exception('Not find this id:' . $id);
            }
        }
    }
    function updateFieldsByID($fields, $id)
    {
    	return $this->update($fields, array('UserID' => $id));
    }
    public function updateImage($id , $imageFile){
    	return $this->update(array('icon' => $imageFile) , array('id' => (int)$id));
    }
    
    public function checkExist($attr, $id = 0)
    {
    	$select = $this->getSql()->select();
    	$select->where($attr);
    
    	if ($id) {
    		$select->where("id <> {$id}");
    	}//echo str_replace('"', '', $select->getSqlString());die;
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
    public function importUpdate($insert, $id)
    {
    	return parent::update($insert, array('id' => $id));
    }
    public function checkExistUrl($url)
    {
    	$select = $this->getSql()->select();
    	if (stripos($url, 'http://') === false) {
    		$url = 'http://'.$url;
    	}
    	$url = trim($url, '/');
    	$select->where("url='{$url}' OR url='{$url}/'");
    	$resultSet = $this->selectWith($select);
    	$total = $resultSet->count();
    	if ($total) {
    		$data = $resultSet->current();
    		return $data->id;
    	} else {
    		return null;
    	}
    }
    
    function formatWhere(array $data){
    	$where = $this->_getSelect()->where;
    
    	if (isset($data['cid'])) {
    		$where->equalTo('rule_code', $data['cid']);
    	}
    	if (isset($data['title'])) {
    		$where->like('pro_rule_type.type_name', '%'.$data['title'].'%');
    	}
    	$this->select->where($where);
    	return $this;
    }
    public function getListToPaginator($order = array())
    {
    	$select = $this->_getSelect();
    	$select->join('pro_rule_type', "pro_rule_type.type_code=pro_rule.rule_code", array('type_name'));
    	if (!empty($order)) {
    		$select->order($order);
    	}//echo str_replace('"', '', $select->getSqlString());die;
    
    	return new DbSelect($select, $this->getAdapter());
    }
    protected function _getSelect(){
    	if(!isset($this->select)){
    		$this->select = $this->getSql()->select();
    	}
    	return $this->select;
    }
    public function getLinkCountByCID($cid) {
    	$select = $this->getSql()->select();
    	$select->where("category={$cid} OR category IN (SELECT nc.id FROM nav_category nc WHERE INSTR(nc.catPath,CONCAT(',',{$cid},',')))");
    	$resultSet = $this->selectWith($select);//echo str_replace('"', '', $select->getSqlString());die;
    	return $resultSet->count();
    }
}