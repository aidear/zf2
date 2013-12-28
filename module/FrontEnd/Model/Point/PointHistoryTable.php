<?php
namespace FrontEnd\Model\Point;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class PointHistoryTable extends TableGateway
{
    protected $table = "point_history";
    
    function getAllToPage($where = array()){
        $select = $this->getSql()->select();
        if ($where) {
        	$select->where($where);
        }
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    public function getTotalPointsCurDay($uid, $code = NULL)
    {
        $select = $this->getSql()->select();
        $select->columns(array('totalPoints' => new Expression("SUM(point_history.points)")));
        if ($code) {
        	$select->join('pro_rule', 'pro_rule.id=point_history.rule_id', array('rule_code'), 'left');
        	$select->where(array('pro_rule.rule_code' => $code));
        }
        $select->where(array('uid' => $uid));
        $select->where("DATE(point_history.add_time) = DATE(NOW())");
        $rs = $this->selectWith($select);//echo str_replace("\"", "", $select->getSqlString()); exit;
        $data = $rs->current();
        
        return isset($data->totalPoints) ? $data->totalPoints : 0;
    }
    function getAll($where = array()){
    	if ($where) {
    		return  $this->select($where);
    	}
        return $this->select();
    }
    
    function getlist($where = array(), $order = NULL)
    {
    	$select = $this->getSql()->select();
    	
    	if ($where) {
    		$select->where($where);
    	}
    	$select->order($order);
    	$resultSet = $this->selectWith($select);//echo str_replace("\"", "", $select->getSqlString()); exit;
    	return $resultSet->toArray();
    }
    public function getArrayItemsByCate()
    {
    	$lists = $this->getlist();
    }
    
    function getOneById($id){
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    
    function getByName($name){
        $select = $this->getSql()->select();
        $where = function (Where $where) use($name){
            $where->like('rule_name' , "$name%");
        };
        $select->where($where);
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function delete($id){
        return parent::delete(array("id" => $id));
    }
    
    function updateFieldsByID($fields, $id)
    {
    	return $this->update($fields, array('uid' => $id));
    }
    public function updateImage($id , $imageFile){
    	return $this->update(array('imgUrl' => $imageFile) , array('id' => (int)$id));
    }
    
    public function checkExist($attr, $UserID = 0)
    {
    	$select = $this->getSql()->select();
    	$select->where($attr);
    
    	if ($UserID) {
    		$select->where("uid <> {$UserID}");
    	}//echo str_replace('"', '', $select->getSqlString());die;
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
}