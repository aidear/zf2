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
namespace BackEnd\Model\Nav;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\Nav\NavCategory;

class LinkTable extends TableGateway
{
    protected $table = "link";
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
    
    function formatWhere(array $data){
    	$where = $this->_getSelect()->where;
    	if(!empty($data['title'])){
    		//     		if('id' == $data['searchType']){
    		//     			$where->equalTo('MerchantFeedConfig.MerchantID', (int)$data['search']);
    		//     		}elseif('name' == $data['searchType']){
    		$where->like('title', '%' . $data['title'] . '%');
    		//     		}
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
}