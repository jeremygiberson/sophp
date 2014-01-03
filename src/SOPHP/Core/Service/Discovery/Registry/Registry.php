<?php


namespace SOPHP\Core\Service\Discovery\Registry;


use SOPHP\Core\Service\Discovery\Registry\Storage\AdapterInterface;
use SOPHP\Core\Service\Service;

class Registry {
    /** @var  AdapterInterface */
    protected $storage;

    /**
     * @param \SOPHP\Core\Service\Discovery\Registry\Storage\AdapterInterface $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return \SOPHP\Core\Service\Discovery\Registry\Storage\AdapterInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param string $interface
     * @param Service $service
     */
    public function addService($interface, Service $service) {
        $this->getStorage()->add($interface, serialize($service));
    }

    /**
     * @param string $interface
     */
    public function removeService($interface) {
        $this->getStorage()->remove($interface);
    }

    /**
     * @param string $interface
     * @return Service
     */
    public function getService($interface) {
        return unserialize($this->getStorage()->get($interface));
    }

    /**
     * @param string $interface
     * @return bool
     */
    public function hasService($interface) {
        return $this->getStorage()->has($interface);
    }

    /**
     * @return Service[]
     */
    public function getList() {
        $services = array();
        foreach($this->getStorage()->getAll() as $key => $value) {
            $services[$key] = unserialize($value);
        }
        return $services;
    }
} 