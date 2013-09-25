<?php
/*
 * package_name : CmusAdapter.php
 * ------------------
 * typecomment
 *
 * PHP versions 5
 * 
 * @Author   : thomas(thomas_fu@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: CmusAdapter.php,v 1.1 2013/04/15 10:56:30 rock Exp $
 */
namespace Custom\Db\Adapter;

class CmusAdapter extends AbstractAdapterServiceFactory
{
    /**
     *
     * @return string
     */
    public function getDbConfigKey()
    {
        return 'cmus';
    }
}