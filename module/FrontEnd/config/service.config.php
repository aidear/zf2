<?php 
use Zend\Session\Container;

use Zend\Session\SessionManager;

use FrontEnd\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
return array(
		'factories' => array(
				'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
				'Zend\Session\SessionManager' => function ($sm) {
	            $config = $sm->get('config');
	            if (isset($config['session'])) {
	                $session = $config['session'];
	    
	                $sessionConfig = null;
	                if (isset($session['config'])) {
	                    $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
	                    $options = isset($session['config']['options']) ? $session['config']['options'] : array();
	                    $sessionConfig = new $class();
	                    $sessionConfig->setOptions($options);
	                }
	    
	                $sessionStorage = null;
	                if (isset($session['storage'])) {
	                    $class = $session['storage'];
	                    $sessionStorage = new $class();
	                }
	    
	                $sessionSaveHandler = null;
	                if (isset($session['save_handler'])) {
	                    // class should be fetched from service manager since it will require constructor arguments
	                    $sessionSaveHandler = $sm->get($session['save_handler']);
	                }
	    
	                $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
	    
	                if (isset($session['validator'])) {
	                    $chain = $sessionManager->getValidatorChain();
	                    foreach ($session['validator'] as $validator) {
	                        $validator = new $validator();
	                        $chain->attach('session.validate', array($validator, 'isValid'));
	    
	                    }
	                }
	            } else {
	                $sessionManager = new SessionManager();
	            }
	            Container::setDefaultManager($sessionManager);
	            return $sessionManager;
	        },
	        'MemberTable' =>  function($sm) {
	        	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	        	$resultSetPrototype = new ResultSet();
	        	$resultSetPrototype->setArrayObjectPrototype(new Model\Users\Member());
	        	return new Model\Users\MemberTable('member', $dbAdapter, null, $resultSetPrototype);
	        },
	        'RegionTable' =>  function($sm) {
	        	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	        	$resultSetPrototype = new ResultSet();
	        	$resultSetPrototype->setArrayObjectPrototype(new Model\Users\Region());
	        	return new Model\Users\RegionTable('region', $dbAdapter, null, $resultSetPrototype);
	        },
	        'NavCategoryTable' =>  function($sm) {
	        	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	        	$resultSetPrototype = new ResultSet();
	        	$resultSetPrototype->setArrayObjectPrototype(new Model\Nav\NavCategory());
	        	return new Model\Nav\NavCategoryTable('nav_category', $dbAdapter, null, $resultSetPrototype);
	        },
	        'LinkTable' =>  function($sm) {
	        	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	        	$resultSetPrototype = new ResultSet();
	        	$resultSetPrototype->setArrayObjectPrototype(new Model\Nav\Link());
	        	return new Model\Nav\LinkTable('link', $dbAdapter, null, $resultSetPrototype);
	        },
		),
);
?>