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

class NavCategoryTable extends TableGateway
{
    protected $table = "nav_category";
    
    function getAllToPage(){
        $select = $this->getSql()->select()->order('order Desc');
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }
    
    function getAll(){
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
}