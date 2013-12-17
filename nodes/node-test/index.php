<?php
/**
 * Simple node using PHP's built-in web server
 */
use Zend\Json\Server\Server;
use Zend\Loader\StandardAutoloader;

chdir(__DIR__);
defined('HOME') ?: define('HOME', realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR);

require_once HOME . 'vendor/autoload.php';

$autoloader = new StandardAutoloader();
$autoloader->registerNamespace('SOPHP', HOME . 'src/SOPHP');
$autoloader->register();


/**
 * determine service to handle from URI
 */
//var_dump($_SERVER['REQUEST_URI'], $_SERVER);

$segments = explode('/', $_SERVER['REQUEST_URI']);
$className = $segments[0];
if(!file_exists($className)){
    return json_encode(array('error' => 'failed to load service ' . $className));
}

$server = new Server();
$server->setClass($className);
echo $server->handle();