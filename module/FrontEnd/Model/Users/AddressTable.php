<?php
namespace FrontEnd\Model\Users;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use FrontEnd\Model\Users\Address;

class AddressTable extends TableGateway
{
    protected $table = "address";
    
    function updateFieldsByID($fields, $id)
    {	$filter =  get_object_vars(new Address());
    	$f = array_keys($filter);
    	foreach ($fields as $k=>$v) {
    		if (!in_array($k, $f)) {
    			unset($fields[$k]);
    		}
    	}
    	return $this->update($fields, array('id' => $id));
    }
    public function insertAddress($fields)
    {
    	$filter = get_object_vars(new Address());
    	$f = array_keys($filter);
    	foreach ($fields as $k=>$v) {
    		if (!in_array($k, $f)) {
    			unset($fields[$k]);
    		}
    	}
    	return $this->insert($fields);
    }
    public function getAddressInfoById($uid, $id)
    {
    	$rs = $this->select(array('user_id' => $uid, 'id' => $id));
    	return $rs ? $rs->current() : null;
    }
    public function addressListByUser($uid)
    {
    	if ($uid) {
    		return $this->select(array('user_id' => $uid));
    	} else {
    		return null;
    	}
    }
    public function checkAddrCount($uid)
    {
    	$total = $this->_getAddrCountByUser($uid);
    	return $total < 20 ? true : false;
    }
    private function _getAddrCountByUser($uid)
    {
    	$rs = $this->select(array('user_id' => $uid));
    	return $rs->count();
    }
}