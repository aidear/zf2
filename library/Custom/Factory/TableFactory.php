<?php
/**
 * TableFactory.php
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
 * @version CVS: Id: TableFactory.php,v 1.0 2013-10-6 下午10:06:53 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Factory;

class TableFactory
{
    public static function factory($tableName){
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new User());
        $gateway = new TableGateway('Sys_User', $this->getServiceLocator()->get('DbAdapter')->getAdapter('smcninternal') , null , $resultSet);
        return new UserTable($gateway);
    }
}