<?php


namespace SOPHP\Core\Service\Discovery\Registry\Storage;


use SOPHP\Core\Registry\Exception\AlreadyRegistered;
use SOPHP\Core\Registry\Exception\RegistrationFailed;
use SOPHP\Zend\Cache\Storage\StorageAwareInterface;
use Zend\Cache\Storage\StorageInterface;

class Cache implements StorageAwareInterface, AdapterInterface {
    const SERVICE_LIST_KEY = '_SOPHP_CACHED_SERVICES';
    const MAX_RETRIES = 3;

    /** @var  StorageInterface */
    protected $cache;

    /**
     * @param StorageInterface $instance
     * @return self
     */
    public function setStorageAdapter(StorageInterface $instance)
    {
        $this->cache = $instance;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorageAdapter()
    {
        return $this->cache;
    }

    /**
     * @param string $key
     * @param string $value
     * @throws \SOPHP\Core\Registry\Exception\RegistrationFailed
     * @throws \SOPHP\Core\Registry\Exception\AlreadyRegistered
     */
    public function add($key, $value)
    {
        $retries = 0;
        do {
            $this->sleep($retries*200);
            $services = $this->internalGet($success, $token);
            if(is_array($services)) {
                if(isset($services[$key]) && $services[$key] == $value) {
                    throw new AlreadyRegistered($key);
                }
                $services[$key] = $value;
            } else {
                $services = array($key => $value);
            }
            $success = $this->internalSet($services, $token);

            if($retries++ >= self::MAX_RETRIES) {
                throw new RegistrationFailed("CAS reached max retries when adding `{$key}`");
            }
        } while(!$success);
    }

    /**
     * @param string $key
     * @throws \SOPHP\Core\Registry\Exception\RegistrationFailed
     */
    public function remove($key)
    {
        if(!$this->has($key)) {
            return;
        }

        $retries = 0;
        do {
            $this->sleep($retries*200);
            $services = $this->internalGet($success, $token);
            if(is_array($services)) {
                if(isset($services[$key])) {
                    unset($services[$key]);
                }
            } else {
                $services = array();
            }
            $success = $this->internalSet($services, $token);

            if($retries++ >= self::MAX_RETRIES) {
                throw new RegistrationFailed("CAS reached max retries when removing `{$key}`");
            }
        } while(!$success);
    }

    /**
     * Get entry for $key, or get all entries if $key is null
     * @param string $key
     * @throws \RuntimeException
     * @return string|null
     */
    public function get($key)
    {
        $services = $this->internalGet($success, $token);
        if(!$success) {
            throw new \RuntimeException("Failed to read services from storage");
        }
        if(!isset($services[$key])) {
            throw new \RuntimeException("`$key` does not exist in storage");
        }
        return $services[$key];
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getAll() {
        $services = $this->internalGet($success, $token);
        if(!$success) {
            throw new \RuntimeException("Failed to read services from storage");
        }
        return $services;
    }

    /**
     * Check if $key exists in storage
     * @param string $key
     * @throws \RuntimeException
     * @return bool
     */
    public function has($key)
    {
        $services = $this->internalGet($success, $token);
        if(!$success) {
            throw new \RuntimeException("Failed to read services from storage");
        }
        return isset($services[$key]);
    }


    /**
     * @param bool $success
     * @param string $casToken
     * @return array
     */
    protected function internalGet(&$success, &$casToken) {
        return unserialize($this->cache->getItem(self::SERVICE_LIST_KEY, $success, $casToken)) ?: array();
    }

    /**
     * @param array $services
     * @param $casToken
     * @return bool
     */
    protected function internalSet(array $services, &$casToken) {
        return $this->cache->checkAndSetItem($casToken, self::SERVICE_LIST_KEY, serialize($services));
    }

    /**
     * @param int $time milliseconds
     */
    protected function sleep($time) {
        sleep($time);
    }
}