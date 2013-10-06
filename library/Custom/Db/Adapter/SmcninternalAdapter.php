<?php
/**
 * SmcninternalAdapter.php
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
 * @version CVS: Id: SmcninternalAdapter.php,v 1.0 2013-10-6 下午10:05:48 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Db\Adapter;

class SmcninternalAdapter extends AbstractAdapterServiceFactory
{
    function getDbConfigKey(){
        return 'smcninternal';
    }
}