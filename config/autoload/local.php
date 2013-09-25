<?php
return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=project;host=localhost',
    	'username' => 'root',
    	'password' => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    		'smcninternal' => array (
    				'driver' => 'Pdo',
    				'dsn' => 'mysql:dbname=project;host=localhost',
    				'username' => 'root',
    				'password' => '',
    				'driver_options' => array (
    						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
    				)
    		),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
	'cache' => array (
			'adapter' => array(
					'name' => 'filesystem',
					'options' => array(
							'cache_dir' => 'data/files/cache',
					)
			),
	),
);