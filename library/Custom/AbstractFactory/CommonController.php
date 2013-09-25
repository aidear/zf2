<?php
/**
 * CommonController.php
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
 * @version CVS: Id: CommonController.php,v 1.0 2013-9-25 下午8:35:24 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\AbstractFactory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommonController implements AbstractFactoryInterface
{
    function canCreateServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName){
        if(class_exists('BackEnd\Controller\\'.ucfirst($requestedName) . 'Controller')){
            return true;
        }
        return false;
    }
    
    function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName){
        $className = 'BackEnd\Controller\\'.ucfirst($requestedName) . 'Controller';
        return new $className;
    }
}