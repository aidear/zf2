<?php
/**
 * RegisterController.php
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
 * @version CVS: Id: RegisterController.php,v 1.0 2013-9-28 下午11:14:18 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Controller;

use Custom\Mvc\ActionEvent;

use BackEnd\Form\AclForm;

use BackEnd\Model\Users\Acl;

use BackEnd\Model\Users\AclTable;

use BackEnd\Model\Users\ResourceTable;

use BackEnd\Model\Users\MyAcl;

use BackEnd\Model\Users\RoleTable;

use Zend\Db\ResultSet\ResultSet;
use Custom\Db\TableGateway\TableGateway;

use BackEnd\Model\Users\Resource;
use BackEnd\Model\Users\Role;
use Custom\Mvc\Controller\AbstractActionController;
use Zend\Cache\StorageFactory;
use Custom\Util\Utilities;


class RegisterController extends AbstractActionController
{
    function indexAction(){
    	$this->layout('layout/register');
        return array();
    }

}