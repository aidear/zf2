<?php
/**
 * Sql.php
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
 * @version CVS: Id: Sql.php,v 1.0 2013-9-25 下午8:37:18 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Db\Sql;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql as ZendSql;

class Sql extends ZendSql
{
    public function setAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
    }
} 
?>