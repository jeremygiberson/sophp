<?php


namespace SOPHP\Core\Registry;


use RuntimeException;
use SOPHP\Core\Service\Contract;
use SOPHP\Zend\Cache\Storage\StorageAwareInterface;
use Zend\Cache\Storage\StorageInterface;

class Registry implements StorageAwareInterface {
    const REGISTERED_SERVICES_KEY = 'registered';
    const MAX_RETRIES = 20;

    /** @var  StorageInterface */
    protected $storage;

    /**
     * @param string $class
     * @return bool
     */
    public function isServiceRegistered($class)
    {
        if(in_array($class, $this->getRegisteredServices())) {

        }
        return false;
    }

    /**
     * @param $class
     * @return Contract
     */
    public function getServiceContract($class)
    {
        return new Contract($class);
    }


    /**
     * @param StorageInterface $instance
     * @return self
     */
    public function setStorageAdapter(StorageInterface $instance)
    {
        $this->storage = $instance;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorageAdapter()
    {
        return $this->storage;
    }

    /**
     * @param null $success
     * @param null $casToken
     * @return array
     */
    public function getRegisteredServices(&$success = null, &$casToken = null)
    {
        return $this->storage->getItem(self::REGISTERED_SERVICES_KEY, $success, $casToken) ?: array();
    }

    /**
     * @param $class
     * @throws \RuntimeException
     */
    public function registerService($class) {
        $key = self::REGISTERED_SERVICES_KEY;
        $retries = 0;
        do {
            $services = $this->getRegisteredServices($success, $token);
            // get failed, why? maybe key no longer available?
            if(!$success && !$this->storage->hasItem($key)) {
                $this->storage->touchItem($key);
                $token = null;
            }
            if(is_array($services)) {
                $services[] = $class;
            } else {
                $services = array($class);
            }
            $success = $this->storage->checkAndSetItem($token, self::REGISTERED_SERVICES_KEY, $services);
            if($retries++ > self::MAX_RETRIES) {
                throw new RuntimeException("Unable to register service - CAS loop terminated after too many retries");
            }
        } while(!$success);
    }
}