<?php
/*
 * package_name : AbstractAdapterServiceFactory.php
 * ------------------
 * doc url https://gist.github.com/ClemensSahs/3917026
 *
 * PHP versions 5
 * 
 * @Author   : thomas(thomas_fu@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: AbstractAdapterServiceFactory.php,v 1.1 2013/04/15 10:56:30 rock Exp $
 */
namespace Custom\Db\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
abstract class AbstractAdapterServiceFactory implements FactoryInterface
{
    /**
     * Create db adapter service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $dbConfigKey = $this->getDbConfigKey();
        if ( false === isset($config['db'][$dbConfigKey]) ) {
            throw new \RuntimeException(sprintf("database config with key '%s' is not found", $dbConfigKey) );
        }
        return new \Zend\Db\Adapter\Adapter($config['db'][$dbConfigKey]);
    }
    
    /**
     * get db key from config
     * @return db type
     */
    abstract function getDbConfigKey();
}