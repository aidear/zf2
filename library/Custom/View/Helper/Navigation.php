<?php
/**
 * Navigation.php
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
 * @version CVS: Id: Navigation.php,v 1.0 2013-9-20 下午8:35:27 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\View\Helper;

use \Zend\View\Helper\Navigation as Father;
class Navigation extends Father
{
    public function getPluginManager()
    {
        if (null === $this->plugins) {
            $this->setPluginManager(new \Custom\View\Helper\Navigation\PluginManager());
        }
        return $this->plugins;
    }
}