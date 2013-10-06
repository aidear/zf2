<?php
/**
 * FrontEndController.php
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
 * @version CVS: Id: FrontEndController.php,v 1.0 2013-10-6 下午10:12:01 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController as Father;
use CommModel\Category\Category;
use Custom\Util\PathManager;

class FrontEndController extends Father
{
    const PAGE   = 15; //每页条数
    const RANGE  = 10; //分页显示数

    /*
     * 初始化数据库表
     */
    public function _getTable($tableName) 
    {
        if (empty($this->$tableName)) {
            $this->$tableName = $this->getServiceLocator()->get($tableName);
        }
        return $this->$tableName;
    }
}