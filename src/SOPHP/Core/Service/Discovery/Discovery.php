<?php


namespace SOPHP\Core\Service\Discovery;


use SOPHP\Core\Service\Discovery\Rpc\Adapter;

class Discovery {
    /** @var  Adapter */
    protected $adapter;

    public function registerService($interface, $implementation) {
        $map = $this->adapter->getServiceMap();
        //$this->registry->add($interface,$map);
    }

    public function unregisterService($interface) {
        $map = $this->adapter->getServiceMap();
        //$this->registry->remove($interface,$map);
    }

    /**
     * @return array
     */
    public function queryForNames() {
        return array();
    }

    /**
     * @param mixed $interface
     * @return mixed
     */
    public function getInstance($interface) {
        $builder = $this->adapter->getProxyBuilder();
        $class = $builder->build($interface);
        $class .= uniqid();
        return new $class();
    }
} 