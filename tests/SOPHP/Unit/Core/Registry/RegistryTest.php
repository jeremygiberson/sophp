<?php


namespace SOPHP\Unit\Core\Registry;

use SOPHP\Core\Registry\Registry;
use SOPHP\Core\Service\Contract;
use SOPHP\Zend\Cache\Storage\StorageMock;
use Zend\Cache\Storage\Adapter;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

class RegistryTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testRegisterServiceContractCasLoopWhenItemChangesAfterGet() {
        $contract = new Contract('foo', new Smd(), '*');
        $token = uniqid();
        $token2 = $token.'_v2';

        $storage = new StorageMock($this);
        $storage->addMethodWill('getItem', array(), 'key', true, $token);
        $storage->addMethodWill('checkAndSetItem', false);
        $storage->mock->expects($this->at(1))
            ->method('checkAndSetItem')
            ->with($token, Registry::REGISTERED_SERVICES_KEY, array($contract));
        $storage->addMethodWill('getItem', array(), 'key', true, $token2);
        $storage->addMethodWill('checkAndSetItem', true);
        $storage->mock->expects($this->at(3))
            ->method('checkAndSetItem')
            ->with($token2, Registry::REGISTERED_SERVICES_KEY, array($contract));


        $registry = new Registry();
        $registry->setStorageAdapter($storage);

        $registry->registerServiceContract($contract);
    }

    /**
     * @expectedException \SOPHP\Core\Registry\Exception\RegistrationFailed
     */
    public function testRegisterServiceContractCasLoopExceptionWhenReachMaxRetries() {
        $contract = new Contract('foo', new Smd(), '*');
        $token = uniqid();

        $storage = new StorageMock($this);
        $storage->setMethodWill('getItem', array(), 'key', true, $token);
        $storage->mock->expects($this->exactly(Registry::MAX_RETRIES+1))
            ->method('getItem');
        $storage->setMethodWill('checkAndSetItem', false);
        $storage->mock->expects($this->exactly(Registry::MAX_RETRIES+1))
            ->method('checkAndSetItem')
            ->with($token, Registry::REGISTERED_SERVICES_KEY, array($contract));

        $registry = new Registry();
        $registry->setStorageAdapter($storage);

        $registry->registerServiceContract($contract);
    }

    public function testRegisterServiceContractInitializesArray() {
        $contract = new Contract('foo', new Smd(), '*');
        $token = uniqid();
        $success = true;

        $storage = new StorageMock($this);
        $storage->addMethodWill('getItem', array(), 'key', $success, $token);
        $storage->addMethodWill('checkAndSetItem', true);
        $storage->mock->expects($this->once())
            ->method('checkAndSetItem')
            ->with($token, Registry::REGISTERED_SERVICES_KEY, array($contract));

        $registry = new Registry();
        $registry->setStorageAdapter($storage);

        $registry->registerServiceContract($contract);
    }

    public function testRegisterServiceContractAppendsArray() {
        $contract = new Contract('foo', new Smd(), '*');
        $contractBar = new Contract('bar', null, '*');
        $token = uniqid();
        $success = true;
        $oldServices = array($contractBar);
        $newServices = array($contractBar, $contract);

        $storage = new StorageMock($this);
        $storage->addMethodWill('getItem', $oldServices, 'key', $success, $token);
        $storage->addMethodWill('checkAndSetItem', true);
        $storage->mock->expects($this->once())
            ->method('checkAndSetItem')
            ->with($token, Registry::REGISTERED_SERVICES_KEY, $newServices);

        $registry = new Registry();
        $registry->setStorageAdapter($storage);

        $registry->registerServiceContract($contract);
    }

    /**
     * @expectedException \SOPHP\Core\Registry\Exception\AlreadyRegistered
     */
    public function testRegisterServiceContractDoesNotAllowDuplicates() {
        $contract = new Contract('foo', new Smd(), '*');
        $token = uniqid();
        $success = true;
        $oldServices = array($contract);

        $storage = new StorageMock($this);
        $storage->addMethodWill('getItem', $oldServices, 'key', $success, $token);

        $registry = new Registry();
        $registry->setStorageAdapter($storage);

        $registry->registerServiceContract($contract);
    }

    /**
     * If this test fails: Bug has been fixed, update code to use setServices w/ services key
     * @expectedException \Zend\Json\Server\Exception\InvalidArgumentException
     */
    public function testSmd() {
        $server = new Server();
        $server->getServiceMap()->setTarget('http://node.example.com');
        $server->getServiceMap()->setDescription('This is a test');
        $server->getServiceMap()->setId(uniqid());
        $server->setClass('SOPHP\Sample\Calculator\Calculator');
        $json = $server->getServiceMap()->toJson();

        $smd = new Smd();
        $options = json_decode($json,true);

        $this->assertArrayNotHasKey('description', $options, 'Bug has been fixed: update code to set on Smd');
        $smd->setServices($options['services']); // broken

        // these should all work
        $smd->setId($options['id']);
        $smd->setOptions($options['services']);
        $smd->setTarget($options['target']);
        $smd->setEnvelope($options['envelope']);
        $smd->setTransport($options['transport']);
        $smd->setContentType($options['contentType']);
    }
}