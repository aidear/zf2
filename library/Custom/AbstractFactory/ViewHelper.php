<?php
/**
 * ViewHelper.php
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
 * @version CVS: Id: ViewHelper.php,v 1.0 2013-9-25 下午8:35:35 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\AbstractFactory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ViewHelper implements AbstractFactoryInterface
{
    
    function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName){
        $className = 'BackEnd\Controller\\'.ucfirst($requestedName) . 'Controller';
        return new $className;
    }
}