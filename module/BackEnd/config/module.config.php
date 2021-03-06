<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'index' => 'BackEnd\Controller\IndexController',
        	'login' => 'BackEnd\Controller\LoginController',
        	'role' => 'BackEnd\Controller\RoleController',
        	'login' => 'BackEnd\Controller\LoginController',
        	'acl' => 'BackEnd\Controller\AclController',
        	'resource' => 'BackEnd\Controller\ResourceController',
        	'user' => 'BackEnd\Controller\UserController',
        	'member' => 'BackEnd\Controller\MemberController',
        	'ajax' => 'BackEnd\Controller\AjaxController',
        	'config' => 'BackEnd\Controller\ConfigController',
        	'nav' => 'BackEnd\Controller\NavController',
        	'site' => 'BackEnd\Controller\SiteController',
        	'promotion' => 'BackEnd\Controller\PromotionController',
        ),
    ),
	// The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                    	'__NAMESPACE__' => 'BackEnd\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'backend' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:controller][/:action]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'BackEnd\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        		
        ),
    ),
    'view_manager' => array(
    	'display_not_found_reason' => true,
    	'display_exceptions'       => true,
    	'doctype'             => 'HTML5',
    	'not_found_template'  => 'error/404',
    	'exception_template'  => 'error/index',
        'template_path_stack' => array(
            'admin' => __DIR__ . '/../View',
        ),
    	'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
	'translator' => array(//配置serverManager中的translator.
			'locale' => 'zh_CN',
			'translation_file_patterns' => array(
					array(
							'type'     => 'phparray',
							'base_dir' => __DIR__ . '/../language',
							'pattern'  => '%s.php',
// 							'text_domain' => 'default',
					),
			),
	),
	'session' => array(
		'config' => array(
				'class' => 'Zend\Session\Config\SessionConfig',
				'options' => array(
						'name' => 'backend',
				),
		),
		'storage' => 'Zend\Session\Storage\SessionStorage',
		'validators' => array(
				array(
						'Zend\Session\Validator\RemoteAddr',
						'Zend\Session\Validator\HttpUserAgent',
				),
		),
	),
);