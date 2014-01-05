<?php
namespace FrontEnd\Model\Point;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class PointHistoryTable extends TableGateway
{
    protected $table = "point_history";
    protected $select;
    
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
    function formatWhere(array $data){
        $where = $this->_getSelect()->where;
    
        if (isset($data['sort'])) {
            switch($data['sort']) {
            	case '1':
            	    $where->greaterThan('points', 0);
            	    break;
            	case '2':
            	    $where->lessThan('points', 0);
            	    break;
            	default:
            	    break;
            }
        }
//         if (isset($data['title'])) {
//             $where->like('pro_rule_type.type_name', '%'.$data['title'].'%');
//         }
        $this->select->where($where);
        return $this;
    }
    public function getListToPaginator($order = array())
    {
        $select = $this->_getSelect();
//         $select->join('pro_rule_type', "pro_rule_type.type_code=pro_rule.rule_code", array('type_name'));
        if (!empty($order)) {
            $select->order($order);
        } else {
        	$select->order(array('add_time' => 'DESC'));
        }
        
        //echo str_replace('"', '', $select->getSqlString());die;
    
        return new DbSelect($select, $this->getAdapter());
    }
    protected function _getSelect(){
        if(!isset($this->select)){
            $this->select = $this->getSql()->select();
        }
        return $this->select;
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