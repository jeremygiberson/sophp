<?php


namespace SOPHP\Framework\Framework\Factory;


use SOPHP\Framework\Framework\Framework;
use SOPHP\Framework\Framework\FrameworkInterface;
use Zend\Config\Config;

class Factory implements FactoryInterface {

    /** @return FrameworkInterface */
    public function newFramework(Config $config)
    {
        return new Framework();
    }
}