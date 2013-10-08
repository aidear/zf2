<?php
/**
 * BackendNavigation.php
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
 * @version CVS: Id: BackendNavigation.php,v 1.0 2013-9-20 下午8:51:15 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Navigation\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Exception;



class BackendNavigation extends AbstractNavigationFactory
{
    function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {

            $application = $serviceLocator->get('Application');
            $routeMatch  = $application->getMvcEvent()->getRouteMatch();
            $router      = $application->getMvcEvent()->getRouter();
            $pages       = $this->getPagesFromConfig(include(APPLICATION_MODULE_PATH . 'config/page.config.php'));

            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }
        return $this->pages;
    }
    
    function getName(){
        return 'backend';
    }
    
    function injectComponents(array $pages, $routeMatch = null, $router = null)
    {
        foreach ($pages as &$page) {
            $hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['route']);
            if ($hasMvc) {
                if (!isset($page['routeMatch']) && $routeMatch) {
                    $page['routeMatch'] = $routeMatch;
                }
                if (!isset($page['router'])) {
                    $page['router'] = $router;
                }
                
                if(!isset($page['route'])){
                    $page['route'] = 'backend';
                }
            }
    
            if (isset($page['pages'])) {
                $page['pages'] = $this->injectComponents($page['pages'], $routeMatch, $router);
            }
        }
        return $pages;
    }
}