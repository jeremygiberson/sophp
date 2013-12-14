<?php


namespace SOPHP\Test\Core\Registry;

use SOPHP\Core\Registry\Registry;
use SOPHP\Zend\Cache\Storage\StorageMock;
use Traversable;
use Zend\Cache\Storage\Adapter;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\StorageInterface;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

class RegistryTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testRegisterServiceCasLoopWhenNothingInStorage() {
        $class = 'foo';
        $token = uniqid();
        $success = true;

        $storage = new StorageMock($this);
        $storage->addMethodWill('getItem', array(), 'key', $success, $token);
        $storage->addMethodWill('checkAndSetItem', true);
        $storage->mock->expects($this->once())
            ->method('checkAndSetItem')
            ->with($token, Registry::REGISTERED_SERVICES_KEY, array($class));

        $registry = new Registry();
        $registry->setStorageAdapter($storage);

        $registry->registerService($class);

    }
    public function testRegisterServiceCasLoopWhenItemChangesAfterGet() {
        $this->markTestIncomplete('todo');
    }
    public function testRegisterServiceCasLoopWhenItemUnsetAfterFailedGet() {
        $this->markTestIncomplete('todo');
    }
    public function testRegisterServiceCasLoopExceptionWhenReachMaxRetries() {
        $this->markTestIncomplete('todo');
    }

    public function testServiceContractSerialized(){
        $this->markTestIncomplete('todo');
    }
    public function testServiceContractUnserialized(){
        $this->markTestIncomplete('todo');
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