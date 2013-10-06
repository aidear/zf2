<?php
/**
 * PluginManager.php
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
 * @version CVS: Id: PluginManager.php,v 1.0 2013-10-6 下午10:18:37 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\View\Helper\Navigation;

use \Zend\View\Helper\Navigation\PluginManager as Father;

class PluginManager extends Father
{
    protected $invokableClasses = array(
        'breadcrumbs' => '\Zend\View\Helper\Navigation\Breadcrumbs',
        'links'       => '\Zend\View\Helper\Navigation\Links',
        'menu'        => '\Custom\View\Helper\Navigation\Menu',
        'sitemap'     => '\Zend\View\Helper\Navigation\Sitemap',
    );
}