<?php
/**
 * AdminLogTable.php
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
 * @version CVS: Id: AdminLogTable.php,v 1.0 2013-11-12 下午11:03:33 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\System;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Custom\Util\Utilities;
use Zend\Session\Container;

class AdminLogTable extends TableGateway
{
    protected $table = "admin_log";
    
    function addLogInfo($data = array())
    {
    	if (!isset($data['ip'])) {
    		$data['ip'] = Utilities::onlineIP();
    	}
    	if (!isset($data['add_time'])) {
    		$data['add_time'] = date('Y-m-d H:i:s');
    	}
    	if (!isset($data['user_id'])) {
    		$container = new Container('user');
    		$data['user_id'] = $container->UserID;
    		$data['user_name'] = $container->Name;
    	}
    	return $this->insert($data);
    }
}