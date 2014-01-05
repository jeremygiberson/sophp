<?php


namespace Unit\Core\Service\Discovery\Registry\Storage;

use SOPHP\Zend\Cache\Storage\StorageMock;
use SOPHP\Core\Service\Discovery\Registry\Storage\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase {
    /** @var  Cache */
    protected $cache;

    public function setUp() {
        parent::setUp();
        $this->cache = new Cache();
    }


    public function testRegisterServiceContractCasLoopWhenItemChangesAfterGet() {
        $key = uniqid('key');
        $value = uniqid('value');
        $token = uniqid();
        $token2 = $token.'_v2';
        $serializedServices =  serialize(array($key => $value));

        $storage = new StorageMock($this);
        $storage->addMethodWill('getItem', array(), Cache::SERVICE_LIST_KEY, true, $token);
        $storage->addMethodWill('checkAndSetItem', false);
        $storage->mock->expects($this->at(1))
            ->method('checkAndSetItem')
            ->with($token, Cache::SERVICE_LIST_KEY, $serializedServices);
        $storage->addMethodWill('getItem', array(), 'key', true, $token2);
        $storage->addMethodWill('checkAndSetItem', true);
        $storage->mock->expects($this->at(3))
            ->method('checkAndSetItem')
            ->with($token2, Cache::SERVICE_LIST_KEY, $serializedServices);

        $this->cache->setStorageAdapter($storage);
        $this->cache->add($key, $value);
    }

    /**
     * @expectedException \SOPHP\Core\Registry\Exception\RegistrationFailed
     */
    public function testRegisterServiceContractCasLoopExceptionWhenReachMaxRetries() {
        $key = uniqid('key');
        $value = uniqid('value');
        $token = uniqid();
        $serializedServices =  serialize(array($key => $value));

        $storage = new StorageMock($this);
        $storage->setMethodWill('getItem', array(), Cache::SERVICE_LIST_KEY, true, $token);
        $storage->mock->expects($this->exactly(Cache::MAX_RETRIES+1))
            ->method('getItem');
        $storage->setMethodWill('checkAndSetItem', false);
        $storage->mock->expects($this->exactly(Cache::MAX_RETRIES+1))
            ->method('checkAndSetItem')
            ->with($token, Cache::SERVICE_LIST_KEY, $serializedServices);

        $this->cache->setStorageAdapter($storage);
        $this->cache->add($key, $value);
    }

}
 