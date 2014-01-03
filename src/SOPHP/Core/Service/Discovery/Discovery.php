<?php


namespace SOPHP\Core\Service\Discovery;

use SOPHP\Core\Service\Discovery\Registry\Registry;
use SOPHP\Core\Service\Service;

class Discovery {
    /** @var  Registry */
    protected $registry;

    /**
     * @param \SOPHP\Core\Service\Discovery\Registry\Registry $registry
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return \SOPHP\Core\Service\Discovery\Registry\Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param string $interface
     * @param Service $service
     */
    public function registerService($interface, Service $service) {
        $this->registry->addService($interface, $service);
    }

    /**
     * todo this should probably be protected by some kind of token so only an
     * authority can remove services (like the server that registered the service
     * or a service manager server)
     * @param string $interface
     */
    public function unregisterService($interface) {
        $this->registry->removeService($interface);
    }

    /**
     * @return array
     */
    public function queryForNames() {
        return array_keys($this->registry->getList());
    }

    /**
     * @param mixed $interface
     * @throws \RuntimeException
     * @return mixed
     */
    public function getInstance($interface) {
        if(!$this->registry->hasService($interface)) {
            throw new \RuntimeException("`$interface` is not a registered service");
        }
        $service = $this->registry->getService($interface);

        // todo return the actual proxy instance of the service
        return $service;
    }
} 