<?php


namespace SOPHP\Framework\Framework\Factory;


use SOPHP\Framework\Framework\FrameworkInterface;
use Zend\Config\Config;

interface FactoryInterface {
    /** @return FrameworkInterface */
    public function newFramework(Config $config);
} 