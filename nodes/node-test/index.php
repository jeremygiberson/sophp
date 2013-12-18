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

$segments = explode('/', $_SERVER['REQUEST_URI']);
$className = $segments[1];

if(!class_exists($className)){
    echo json_encode(array('error' => 'failed to load service ' . $className));
    return;
}

$server = new Server();
$server->setClass($className);

if('GET' == $_SERVER['REQUEST_METHOD']) {
    //$server->setTarget($_SERVER['REQUEST_URI']);
    $smd = $server->getServiceMap();
    $smd->setTarget($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI']);
    header('Content-Type: application/json');
    echo $smd;

    return;
}

echo $server->handle();