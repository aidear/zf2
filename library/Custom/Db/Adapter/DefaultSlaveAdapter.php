<?php
/*
* package_name : file_name
* ------------------
* typecomment

*
* PHP versions 5
* 
* @Author   : thomas fu(tfu@mezimedia.com)
* @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com <http://www.mezimedia.com/> )
* @license  : http://www.mezimedia.com/license/
* @Version  : CVS: $Id: DefaultSlaveAdapter.php,v 1.1 2013/04/15 10:56:30 rock Exp $
*/
namespace Custom\Db\Adapter;

class DefaultSlaveAdapter extends AbstractAdapterServiceFactory
{
    /**
     *
     * @return string
     */
    public function getDbConfigKey()
    {
        return 'defaultSlave';
    }
}
?>