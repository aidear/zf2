<?php
namespace BackEnd;
use BackEnd\Model\Admin;
use BackEnd\Model\AdminTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Session\SessionManager;
use Zend\Session\Container;
// use Zend\Mvc\ModuleRouteListener;
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
		$eventManager->attach('render', array($this, 'setLayoutTitle'));
		
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
		$translator = $e->getApplication()->getServiceManager()->get('translator');
		$translator
		->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		->setFallbackLocale('zh_CN');
		$sys_config = array();
		if (file_exists('./data/sys_config.php')) {
			$sys_config = include'./data/sys_config.php';
		}
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
	public function setLayoutTitle($e)
	{
		$matches    = $e->getRouteMatch();
		$action     = $matches->getParam('action');
		$controller = $matches->getParam('controller');
		$module     = __NAMESPACE__;
		$siteName   = 'ECMS V1.0';
	
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