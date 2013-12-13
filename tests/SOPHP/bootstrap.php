<?php
use Zend\Loader\StandardAutoloader;

chdir(__DIR__);
defined('HOME') ?: define('HOME', realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR);

require_once HOME . 'vendor/autoload.php';

$autoloader = new StandardAutoloader();
$autoloader->registerNamespace('SOPHP', HOME . 'src/SOPHP');
$autoloader->register();