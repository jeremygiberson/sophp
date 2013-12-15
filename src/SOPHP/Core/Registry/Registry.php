<?php


namespace SOPHP\Core\Registry;

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
     * @param $class
     * @return bool
     */
    public function isServiceRegistered($class)
    {
        $contract = new Contract($class, null, '*');
        return $this->isContractInList($contract, $this->getRegisteredServices());
    }

    /**
     * @param $class
     * @return Contract
     */
    public function getServiceContract($class)
    {
        $contract = new Contract($class, null, '*');
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
     * @param Contract $contract
     * @throws Exception\RegistrationFailed
     * @throws \InvalidArgumentException
     * @throws Exception\AlreadyRegistered
     */
    public function registerServiceContract(Contract $contract) {
        $this->validateContract($contract);
        $key = self::REGISTERED_SERVICES_KEY;
        $retries = 0;
        do {
            $services = $this->getRegisteredServices($success, $token);
            if(is_array($services)) {
                if($this->isContractInList($contract, $services)) {
                    throw new AlreadyRegistered($contract);
                }
                $services[] = $contract;
            } else {
                $services = array($contract);
            }
            $success = $this->storage->checkAndSetItem($token, $key, $services);
            if($retries++ >= self::MAX_RETRIES) {
                throw new RegistrationFailed("CAS reached max retries for [{$contract->getClassName()}]");
            }
        } while(!$success);
    }

    /**
     * Validate the attributes of a contract
     * @param Contract $contract
     * @param bool $requireVersion
     * @param bool $requireSmd
     * @param bool $requireClassName
     * @throws \InvalidArgumentException
     */
    protected function validateContract(Contract $contract, $requireVersion = true, $requireSmd = true, $requireClassName = true) {
        if(!$contract) {
            throw new \InvalidArgumentException("Contract must be provided");
        }
        if($requireClassName && !$contract->getClassName()) {
            throw new \InvalidArgumentException("Class name must be provided in the contract");
        }
        if($requireSmd && !$contract->getSmd()) {
            throw new \InvalidArgumentException("SMD must be provided in the contract");
        }
        if($requireVersion && !$contract->getVersion()) {
            throw new \InvalidArgumentException("Version must be provided in the contract");
        }
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
     * todo come up with a better method name
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