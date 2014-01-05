<?php
namespace Unit\Core\Service\Discovery\Registry;

use PHPUnit_Framework_TestCase;
use SOPHP\Core\Service\Builder\Rpc\Json;
use SOPHP\Core\Service\Discovery\Registry\Registry;
use SOPHP\Core\Service\Discovery\Registry\Storage\AdapterInterface;
use SOPHP\Core\Service\Discovery\Registry\Storage\Memory;
use Zend\Uri\Uri;

class RegistryTest extends PHPUnit_Framework_TestCase {
    const TestInterface = '\SOPHP\Sample\Calculator\CalculatorInterface';
    /** @var  Registry */
    protected $registry;
    /** @var  AdapterInterface */
    protected $storage;
    public function setUp() {
        parent::setUp();
        $this->storage = new Memory();
        $this->registry = new Registry();
        $this->registry->setStorage($this->storage);
    }

    public function testAddService() {
        $service = $this->getService();
        $serialized = serialize($service);
        $this->registry->addService(self::TestInterface, $service);
        $this->assertTrue($this->storage->has(self::TestInterface));
        $test = $this->storage->get(self::TestInterface);
        $this->assertEquals($serialized, $test);
    }

    public function testRemoveService() {
        $service = $this->getService();
        $this->registry->addService(self::TestInterface, $service);
        $this->assertTrue($this->storage->has(self::TestInterface));
        $this->registry->removeService(self::TestInterface);
        $this->assertFalse($this->storage->has(self::TestInterface));
    }

    public function testGetAllServices() {
        $service = $this->getService();
        $services = array(self::TestInterface=>$service);
        $this->registry->addService(self::TestInterface, $service);
        $test = $this->registry->getAllServices();
        $this->assertEquals($services, $test);
    }

    /**
     * @return \SOPHP\Core\Service\Service
     */
    protected function getService() {
        $serviceBuilder = new Json();
        return $serviceBuilder->build(new Uri('http://foo'), self::TestInterface, null);
    }
}
 