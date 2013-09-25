<?php
/**
 * SqlLog.php
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
 * @version CVS: Id: SqlLog.php,v 1.0 2013-9-25 下午8:36:53 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\Db;

class SqlLog
{
    static private $sqls;
    
    public function add($sql)
    {
        self::$sqls .= $sql . "\n";
    }
    
    public function get()
    {
        $re = self::$sqls;
        self::$sqls = '';
        return $re;
    }
}