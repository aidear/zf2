<?php
namespace FrontEnd\Model\Users;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use FrontEnd\Model\Users\Member;

class MemberTable extends TableGateway
{
    protected $table = "member";
    
    function updateFieldsByID($fields, $id)
    {	$filter =  get_object_vars(new Member());
    	$f = array_keys($filter);
    	foreach ($fields as $k=>$v) {
    		if (!in_array($k, $f)) {
    			unset($fields[$k]);
    		}
    	}
    	return $this->update($fields, array('UserID' => $id));
    }
    public function updateImage($UserID , $imageFile){
    	return $this->update(array('ImgUrl' => $imageFile) , array('UserID' => (int)$UserID));
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
    function getOneForId($id){
    	if (is_array($id)) {
    		$select = $this->getSql()->select();
    		$where = function(Where $where) use ($id) {
    			$where->in('UserID', $id);
    		};
    		$select->where($where);
    		return $this->selectWith($select);
    	} else {
    		$rowset = $this->select(array('UserID' => $id));
    		$row = $rowset->current();
    		return $row;
    	}
    }
    function getOneById($id){
    	$rowset = $this->select(array('UserID' => $id));
    	$row = $rowset->current();
    	return $row;
    }
    function getUserByUserName($userName)
    {
    	$rowset = $this->select("(UserName='{$userName}' OR Email='{$userName}') AND Status=1");
    	$row = $rowset->current();
    	return $row;
    }
    public function updateUserPoint($uid, $point, $history)
    {
        $keys = array_keys($history);
        $fields = "";
        $values = "";
        foreach($keys as $key) {
            if($fields != "") {
                $fields .= ",";
                $values .= ",";
            }
            $fields .= '`'.$key.'`';
            $values .= "'".$history[$key]."'";
        }
        $str = "($fields) VALUES ($values)";
        $sql = "UPDATE member SET Points=Points+{$point} WHERE UserID={$uid};";
        $sql .= "INSERT INTO point_history {$str}";
        try {
            $connect = $this->getAdapter()->getDriver()->getConnection();
            $connect->beginTransaction();
            $this->getAdapter()->query($sql);
            $connect->commit();
            return true;
        } catch (\Exception $e) {
            if ($connect instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                $connect->rollback();
            }
            return false;
        }
//         return $this->getAdapter()->query($sql);
    }
}