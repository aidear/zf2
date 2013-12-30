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
namespace FrontEnd\Model\Nav;


use Custom\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use FrontEnd\Model\Nav\NavCategory;

class NavCategoryTable extends TableGateway
{
    protected $table = "nav_category";
    
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
        return parent::delete(array("UserID" => $id));
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
}