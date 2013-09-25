<?php
/*
* package_name : FrontEndController.php
* ------------------
* typecomment
*
* PHP versions 5
* 
* @Author   : Richie Zhang(rizhang@mezimedia.com)
* @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
* @license  : http://www.mezimedia.com/license/
* @Version  : CVS: $Id: FrontEndController.php,v 1.1 2013/04/15 10:56:30 rock Exp $
*/
namespace Custom\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController as Father;
use CommModel\Category\Category;
use Custom\Util\PathManager;

class FrontEndController extends Father
{
    const PAGE   = 15; //每页条数
    const RANGE  = 10; //分页显示数
    const SITEID = 1;  //站点id

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