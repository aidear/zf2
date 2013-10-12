<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'index' => 'FrontEnd\Controller\IndexController',
        	'login' => 'FrontEnd\Controller\LoginController',
        	'engine' => 'FrontEnd\Controller\EngineController',
        	'login' => 'FrontEnd\Controller\LoginController',
        	'register' => 'FrontEnd\Controller\RegisterController',
//         	'resource' => 'BackEnd\Controller\ResourceController',
//         	'user' => 'BackEnd\Controller\UserController',
        	'member' => 'FrontEnd\Controller\MemberController',
        	'ajax' => 'FrontEnd\Controller\AjaxController',
//         	'config' => 'BackEnd\Controller\ConfigController',
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
                    	'__NAMESPACE__' => 'FrontEnd\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'shortcut' => array(
        			'type' => 'Zend\Mvc\Router\Http\Literal',
        			'options' => array(
        					'route'    => '/shortcut',
        					'defaults' => array(
        							'controller' => 'index',
        							'action'     => 'shortcut',
        					),
        				),
        	),
        	'engine' => array(
        		'type' => 'Zend\Mvc\Router\Http\Literal',
        		'options' => array(
        			'route' => '/engine_search',
        			'defaults' => array(
        				'controller' => 'engine',
        				'action' => 'index',
        			)
        		)					
        	),
        		'register' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/register',
        						'defaults' => array(
        								'controller' => 'register',
        								'action' => 'index',
        						)
        				)
        		), 

         'captcha' => array(
        		'type' => 'Zend\Mvc\Router\Http\Literal',
         		'options' => array(
         			'route' => '/register-captcha',
         			'defaults' => array(
         				'controller' => 'register',
         				'action' => 'captcha',
         			)
         		)					
         ),
        		'forgetCaptcha' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/forget-captcha',
        						'defaults' => array(
        								'controller' => 'member',
        								'action' => 'captcha',
        						)
        				)
        		),
        		'forget-confirm' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/forget-confirm.do',
        						'defaults' => array(
        								'controller' => 'member',
        								'action' => 'confirm',
        						)
        				)
        		),
        		'email' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/register-email',
        						'defaults' => array(
        								'controller' => 'register',
        								'action' => 'email',
        						)
        				)
        		),   
        		'regSend' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/register-n',
        						'defaults' => array(
        								'controller' => 'register',
        								'action' => 'next',
        						)
        				)
        		),
        		'regSuc' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/register-success',
        						'defaults' => array(
        								'controller' => 'register',
        								'action' => 'success',
        						)
        				)
        		),
        		'login' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/login',
        						'defaults' => array(
        								'controller' => 'login',
        								'action' => 'index',
        						)
        				)
        		),
        		'logout' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/logout',
        						'defaults' => array(
        								'controller' => 'login',
        								'action' => 'logout',
        						)
        				)
        		),
        		'forget_pass' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/forget_password/',
        						'defaults' => array(
        								'controller' => 'member',
        								'action' => 'findPassword',
        						)
        				)
        		),
        		'ajax' => array(
        				'type' => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route' => '/ajax',
        						'defaults' => array(
        								'controller' => 'ajax',
        								'action' => 'check',
        						)
        				)
        		),
        		/*
            'FrontEnd' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:controller][/:action]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'FrontEnd\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        		*/
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
	'translator' => array(
			'locale' => 'zh_CN',
			'translation_file_patterns' => array(
					array(
							'type'     => 'gettext',
							'base_dir' => __DIR__ . '/../language',
							'pattern'  => '%s.php',
							'text_domain' => __NAMESPACE__,
					),
			),
	),
	'session' => array(
		'config' => array(
				'class' => 'Zend\Session\Config\SessionConfig',
				'options' => array(
						'name' => 'frontend',
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