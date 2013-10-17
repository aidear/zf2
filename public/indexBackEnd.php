<?php
/**
 * Display all errors when APPLICATION_ENV is development.
 */
if ($_SERVER['APPLICATION_ENV'] == 'development') {
    error_reporting(E_ALL & ~E_STRICT);
    ini_set("display_errors", 1);
}
define('APPLICATION_MODULE_PATH', dirname(__DIR__) . '/module/BackEnd/');
define('APPLICATION_PATH', dirname(__DIR__));
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'config/init_autoloader.php';
require_once 'vendor/PHPExcel.php';

$globalConfig = require 'config/application.config.php';
$globalConfig['modules'] = array('BackEnd');

// Run the application!
Zend\Mvc\Application::init($globalConfig)->run();