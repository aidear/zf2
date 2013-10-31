<?php
namespace FrontEnd\Model\Users;


use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class IdentityTable extends TableGateway
{
    protected $table = "identity_record";
    
    function updateFieldsByID($fields, $id)
    {	$filter =  get_object_vars(new Identity());
    	$f = array_keys($filter);
    	foreach ($fields as $k=>$v) {
    		if (!in_array($k, $f)) {
    			unset($fields[$k]);
    		}
    	}
    	return $this->update($fields, array('UserID' => $id));
    }
    
    public function checkExist($attr, $UserID = 0)
    {
    	$select = $this->getSql()->select();
    	$select->where($attr);
    
    	if ($UserID) {
    		$select->where("user_id <> {$UserID}");
    	}//echo str_replace('"', '', $select->getSqlString());die;
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
    function getOneForId($id){
    	if (is_array($id)) {
    		$select = $this->getSql()->select();
    		$where = function(Where $where) use ($id) {
    			$where->in('user_id', $id);
    		};
    		$select->where($where);
    		return $this->selectWith($select);
    	} else {
    		$rowset = $this->select(array('user_id' => $id));
    		$row = $rowset->current();
    		return $row;
    	}
    }
    function getOneByUID($id){
    	$rowset = $this->select(array('user_id' => $id));
    	$row = $rowset->current();
    	return $row;
    }
}