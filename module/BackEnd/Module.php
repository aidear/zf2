<?php
namespace BackEnd;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use BackEnd\Model\Users\MyAcl;
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
		
		$eventManager->attach(MvcEvent::EVENT_ROUTE, function($e) use ($eventManager){
            $sm = $e->getApplication()->getServiceManager();
            $route = $e->getRouteMatch();
            $controller = $route->getParam('controller');
            $action = $route->getParam('action');
            $session = new Container('user');
            if($controller && $action){
                //验证是否登录
                if($controller == 'login' || ($controller == 'acl' && $action == 'clearCache') 
                    || $controller == 'ajax'
                    || (isset($session->UserID) && $session->Role == 'admin')){
                    return;
                }
                //远程调用系统方法
                if ($controller == 'system') {
                    return;
                }
                if(!isset($session->UserID)){
                    header('Location:/login');
                    exit;
                }
                //admin log
                $adminLogTable = $sm->get('AdminLogTable');
                $params = $e->getRequest()->getQuery()->toArray();
                $log_info = "Controller:[{$controller}];Action:[{$action}];Query:[".http_build_query($params).']';
                $logInfo = array('user_id' => $session->UserID, 'user_name' => $session->Name, 'opt_type' => 3, 'info' => $log_info);
                $adminLogTable->addLogInfo($logInfo);
                //验证ACL
                
                $controller = strtolower($controller);
                $resource = $controller . '_' . $action;
                $myAcl = $sm->get('acl');
                try{
                    if(empty($session->Role) || !$myAcl->acl->isAllowed($session->Role 
                        , $resource)){
                        throw new \Exception('Not Allow');
                    }
                }catch(\Exception $err){
                    $e->setError('error');
                    $e->setParam('exception', $err);
                    $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
                }
                
            }
        });
// 		$translator = $e->getApplication()->getServiceManager()->get('translator');
// 		$translator
// 		->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))
// 		->setFallbackLocale('zh_CN');

		//version 2.2
		$translator = new \Zend\Mvc\I18n\Translator();
		$translator->addTranslationFilePattern ( 'phparray' , __DIR__.'/language/','%s.php')->setFallbackLocale('zh_CN');
		\Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
		$sys_config = array();
		if (file_exists('./data/sys_config.php')) {
			$sys_config = include'./data/sys_config.php';
		}
		define("__SHOP_URL", isset($sys_config['shop_url']) ? $sys_config['shop_url'] : '');
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
		$matches    = $e->getRouteMatch();
		
		$module     = __NAMESPACE__;
		$siteName   = 'ECMS V1.0';
	
		// Getting the view helper manager from the application service manager
		$viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
	
		// Getting the headTitle helper from the view helper manager
		$headTitleHelper   = $viewHelperManager->get('headTitle');
	
		// Setting a separator string for segments
		$headTitleHelper->setSeparator(' - ');
	
		// Setting the action, controller, module and site name as title segments
		$action = '';
		$controller = '';
		if ($matches) {
		    $action     = $matches->getParam('action') ? $matches->getParam('action') : 'index';
		    $controller = $matches->getParam('controller');
		    
		    $headTitleHelper->append($action);
		    $headTitleHelper->append($controller);
		}
		
		//$headTitleHelper->append($module);
		$headTitleHelper->append($siteName);
		$queryParams = $e->getRequest()->getQuery()->toArray();
		
		$viewModel = $e->getViewModel();
		if (!($viewModel->terminate())) {
			$viewModel->setVariables($queryParams);
			$translator = $e->getApplication()->getServiceManager()->get('translator');
			$viewModel->setVariables(array('tabName' => $translator->translate($controller.'_'.$action),
			    'tabCode' => $controller.'_'.$action
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