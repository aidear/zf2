<?php
namespace FrontEnd\Model\Nav;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Like;

class RecommendLinkTable extends TableGateway
{
    protected $table = "recommend_link";
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
    
    function getlist($where = array(), $order = 'order Desc')
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
    
	function save(RecommendLink $reLink){
	    $link = $reLink->toArray();
        unset($link['inputFilter']);
        unset($link['category']);
        if(empty($link['id'])){
            unset($link['id']);
            if ($this->insert($link)) {
            	return $this->getLastInsertValue();
            }
        }else{
            $id = $link['id'];
            if($this->getOneById($id)){
                unset($link['id']);
                unset($link['addTime']);
                $this->update($link , array('id' => $id));
            }else{
                throw new \Exception('Not find this id:' . $id);
            }
        }
    }
    function updateFieldsByID($fields, $id)
    {
    	return $this->update($fields, array('UserID' => $id));
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
    protected function _getSelect(){
    	if(!isset($this->select)){
    		$this->select = $this->getSql()->select();
    	}
    	return $this->select;
    }
}