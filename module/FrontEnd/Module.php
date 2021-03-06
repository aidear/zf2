<?php
namespace FrontEnd;

use Zend\Session\Container;
use Zend\EventManager\EventInterface as Event;
class Module
{
	public function onBootstrap(Event $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$serviceManager      = $e->getApplication()->getServiceManager();
		$this->bootstrapSession($e);
		
		ini_set('date.timezone', 'Asia/Shanghai');
		
		// Register a render event:Title
		$eventManager->attach('render', array($this, 'setDefaultView'));
		
		
// 		$translator = $e->getApplication()->getServiceManager()->get('translator');
// 		$translator
// 		->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))
// 		->setFallbackLocale('zh_CN');
// 		$translator = new \Zend\Mvc\I18n\Translator();
// 		$translator->addTranslationFile ( 'phparray' , __DIR__.'/language/zh_CN.php');
// 		\Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
		$sys_config = array();
		if (file_exists('./data/sys_config.php')) {
			$sys_config = include'./data/sys_config.php';
		}
		$this->siteConfg = $sys_config;
		define("__SHOP_URL", isset($sys_config['shop_url']) ? $sys_config['shop_url'] : '');
		define("__SHOP_TITLE", isset($sys_config['shop_title']) ? $sys_config['shop_title'] : '');
		define('__LIST_ORDER', 'ASC');
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
		$module     = __NAMESPACE__;
		$siteName   = $this->siteConfg['shop_title'];
	
		// Getting the view helper manager from the application service manager
		$viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
	
		// Getting the headTitle helper from the view helper manager
		$headTitleHelper   = $viewHelperManager->get('headTitle');
	
		// Setting a separator string for segments
		$headTitleHelper->setSeparator(' - ');
	
		// Setting the action, controller, module and site name as title segments
		$matches    = $e->getRouteMatch();
		$action = '';
		$controller = '';
		if ($matches) {
			$action     = $matches->getParam('action');
			$controller = $matches->getParam('controller');
			$headTitleHelper->append($action);
			$headTitleHelper->append($controller);
		}
		//$headTitleHelper->append($module);
		$headTitleHelper->append($siteName);
		
		$container = new Container('member');
		
		$viewModel = $e->getViewModel();
		if (!($viewModel->terminate())) {
			$viewModel->setVariables(array(
					'siteConfg' => $this->siteConfg,
					'container' => $container,
			        'controller' => $controller,
			        'action' => $action,
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
    	return include __DIR__ . '/config/service.config.php';
    }
    public function getViewHelperConfig(){
    	return include __DIR__ . '/config/viewhelper.config.php';
    }
    
//     public function getControllerConfig(){
//     	return include __DIR__ . '/Config/controller.config.php';
//     }
}