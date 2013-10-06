<?php
namespace FrontEnd;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Session\SessionManager;
use Zend\Session\Container;
// use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface as Event;

class Module
{
	public function onBootstrap(Event $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$serviceManager      = $e->getApplication()->getServiceManager();
		$this->bootstrapSession($e);
		
		// Register a render event:Title
		$eventManager->attach('render', array($this, 'setDefaultView'));
		
		
		$translator = $e->getApplication()->getServiceManager()->get('translator');
		$translator
		->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		->setFallbackLocale('zh_CN');
		$sys_config = array();
		if (file_exists('./data/sys_config.php')) {
			$sys_config = include'./data/sys_config.php';
		}
		$this->siteConfg = $sys_config;
		define("__SHOP_URL", isset($sys_config['shop_url']) ? $sys_config['shop_url'] : '');
	}
	
	public function bootstrapSession($e)
	{
		$session = $e->getApplication()
		->getServiceManager()
		->get('Zend\Session\SessionManager');
		$session->start();
	
		$container = new Container('initialized');
		if (!isset($container->init)) {
			$session->regenerateId(true);
			$container->init = 1;
		}
	}
	/**
	 * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
	 * @return void
	 */
	public function setDefaultView($e)
	{
		$matches    = $e->getRouteMatch();
		$action     = $matches->getParam('action');
		$controller = $matches->getParam('controller');
		$module     = __NAMESPACE__;
		$siteName   = $this->siteConfg['shop_title'];
	
		// Getting the view helper manager from the application service manager
		$viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
	
		// Getting the headTitle helper from the view helper manager
		$headTitleHelper   = $viewHelperManager->get('headTitle');
	
		// Setting a separator string for segments
		$headTitleHelper->setSeparator(' - ');
	
		// Setting the action, controller, module and site name as title segments
		$headTitleHelper->append($action);
		$headTitleHelper->append($controller);
		//$headTitleHelper->append($module);
		$headTitleHelper->append($siteName);
		
		$container = new Container('member');
		
		$viewModel = $e->getViewModel();
		if (!($viewModel->terminate())) {
			$viewModel->setVariables(array(
					'siteConfg' => $this->siteConfg,
					'container' => $container,
			));
		}
	}
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getServiceConfig()
    {
    	return include __DIR__ . '/Config/service.config.php';
    }
    public function getViewHelperConfig(){
    	return include __DIR__ . '/Config/viewhelper.config.php';
    }
    
//     public function getControllerConfig(){
//     	return include __DIR__ . '/Config/controller.config.php';
//     }
}