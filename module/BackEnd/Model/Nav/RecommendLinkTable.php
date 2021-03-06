<?php
namespace BackEnd\Model\Nav;


use Custom\Paginator\Adapter\DbSelect;

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
    
	function save(Link $link){
        $link = $link->toArray();
        unset($link['inputFilter']);
        unset($link['imgUrl']);
        if(empty($link['id'])){
            $rowset = $this->select(array('title' => $link['title']));
            if($rowset->count() < 1){
                if ($this->insert($link)) {
                	return $this->getLastInsertValue();
                }
            }
            return;
        }
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
    	return $this->update($fields, array('id' => $id));
    }
    function deleteMuti($where) {
        return parent::delete($where);
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
    
    function formatWhere(array $data){
    	$where = $this->_getSelect()->where;
    	if(!empty($data['k'])){
    		$where->like('title', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('url', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('QQ', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('user_name', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('email', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('mobile', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('description', '%' . $data['k'] . '%');
    		$where->or;
    		$where->like('note', '%' . $data['k'] . '%');
    	}
    	if(!empty($data['cid'])){
    		$where->equalTo('link.category', $data['cid']);
    	}
    	//     	if(isset($data['AffiliateID']) && $data['AffiliateID'] >= 0){
    	//     		$where->equalTo('MerchantFeedConfig.AffiliateID', $data['AffiliateID']);
    	//     	}
    
    	$this->select->where($where);
    	return $this;
    }
    public function getListToPaginator($order = array())
    {
    	$select = $this->_getSelect();
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