<?php
/**
 * DefaultAdapter.php
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
 * @version CVS: Id: DefaultAdapter.php,v 1.0 2013-10-6 下午10:05:32 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Db\Adapter;

class DefaultAdapter extends AbstractAdapterServiceFactory
{
    /**
     *
     * @return string
     */
    public function getDbConfigKey()
    {
        return 'default';
    }
}