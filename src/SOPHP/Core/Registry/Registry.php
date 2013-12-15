<?php


namespace SOPHP\Core\Registry;


use RuntimeException;
use SOPHP\Core\Registry\Exception\AlreadyRegistered;
use SOPHP\Core\Registry\Exception\RegistrationFailed;
use SOPHP\Core\Service\Contract;
use SOPHP\Zend\Cache\Storage\StorageAwareInterface;
use Zend\Cache\Storage\StorageInterface;

class Registry implements StorageAwareInterface {
    const REGISTERED_SERVICES_KEY = 'registered';
    const MAX_RETRIES = 20;

    /** @var  StorageInterface */
    protected $storage;

    /**
     * Check if service has been registered. Optionally supply a contract to look for a specific version, otherwise
     * any registered version is matched
     * @param $class
     * @param Contract $contract
     * @return bool
     */
    public function isServiceRegistered($class, Contract $contract = null)
    {
        if(!$contract) {
            $contract = new Contract($class, null, '*');
        }
        return $this->isContractInList($contract, $this->getRegisteredServices());
    }

    /**
     * @param $class
     * @param Contract $contract
     */
    public function getServiceContract($class, Contract $contract = null)
    {
        if(!$contract) {
            $contract = new Contract($class, null, '*');
        }
        foreach($this->getRegisteredServices() as $registeredContract) {
            if($this->doContractsMatch($contract, $registeredContract)) {
                return $registeredContract;
            }
        }
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
     * @param $contract
     * @throws Exception\RegistrationFailed
     * @throws Exception\AlreadyRegistered
     */
    public function registerService($class, $contract) {
        $key = self::REGISTERED_SERVICES_KEY;
        $retries = 0;
        do {
            $services = $this->getRegisteredServices($success, $token);
            if(is_array($services)) {
                if($this->isContractInList($contract, $services)) {
                    throw new AlreadyRegistered($contract);
                }
                $services[] = $class;
            } else {
                $services = array($class);
            }
            $success = $this->storage->checkAndSetItem($token, $key, $services);
            if($retries++ >= self::MAX_RETRIES) {
                throw new RegistrationFailed("CAS reached max retries for [$class]");
            }
        } while(!$success);
    }

    /**
     * @param Contract $contract
     * @param Contract[] $list
     * @return bool
     */
    protected function isContractInList(Contract $contract, $list) {
        foreach($list as $contractInList) {
            if($this->doContractsMatch($contract, $contractInList)){
                return true;
            }
        }
        return false;
    }

    /**
     * Compare two contracts
     * @param Contract $contract
     * @param Contract $test
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function doContractsMatch(Contract $contract, Contract $test) {
        if(!$contract) {
            throw new \InvalidArgumentException("\$contract must be provided");
        }
        if(!$test) {
            throw new \InvalidArgumentException("\$test must be provided");
        }
        if($test->getClassName() == $contract->getClassName()
            // if md5 is not set, we are looking for any version
            && ($contract->getMd5() == null
                // otherwise we're looking for a specific version
                || $test->getMd5() == $contract->getMd5())) {
            return true;
        }
        return false;
    }
}