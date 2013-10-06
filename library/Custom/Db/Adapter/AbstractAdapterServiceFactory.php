<?php
/**
 * AbstractAdapterServiceFactory.php
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
 * @version CVS: Id: AbstractAdapterServiceFactory.php,v 1.0 2013-10-6 下午10:01:38 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
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