<?php


namespace SOPHP\Core\Service\Discovery;

use SOPHP\Core\Service\Discovery\Exception\ServiceNotRegistered;
use SOPHP\Core\Service\Discovery\Registry\Registry;
use SOPHP\Core\Service\Provider\Provider;
use SOPHP\Core\Service\Provider\Strategy;
use SOPHP\Core\Service\Service;

class Discovery {
    /** @var  Registry */
    protected $registry;
    /** @var  Provider */
    protected $provider;

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
     * @param \SOPHP\Core\Service\Provider\Provider $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return \SOPHP\Core\Service\Provider\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }



    /**
     * @param string $interface
     * @param Service $service
     */
    public function registerService($interface, Service $service) {
        $this->registry->addService($interface, $service);
        $this->getProvider()->setStrategyPreference($service, new Strategy(Strategy::Local));
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
        return array_keys($this->registry->getAllServices());
    }

    /**
     * @param mixed $interface
     * @throws ServiceNotRegistered
     * @return mixed
     */
    public function getInstance($interface) {
        if(!$this->registry->hasService($interface)) {
            throw new ServiceNotRegistered("`$interface` is not a registered service");
        }
        $service = $this->registry->getService($interface);

        return $this->provider->getInstance($service);
    }
} 