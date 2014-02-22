<?php


namespace SOPHP\Test\Integration\Core\Service\Discovery;


use SOPHP\Core\Service\Discovery\Discovery;
use SOPHP\Sample\Calculator\CalculatorInterface;
use SOPHP\Test\TestCase\WebServer;

class DiscoveryTest extends WebServer {
    const TEST_INTERFACE = '\SOPHP\Sample\Calculator\CalculatorInterface';
    const PROXY_CLASS = 'SOPHP\Core\Proxy\Proxy';

    public function setUp() {
        parent::setUp();
    }

    /**
     * @return Discovery
     */
    protected function getDiscoveryServiceInstance() {
        $this->markTestIncomplete("need to get discovery service w/ DI satisfied");
    }

    /**
     * Expect to see CalculatorInterface returned which is registered in
     * the node-test server spawned by the integration test.
     */
    public function testQueryForNamesReturnsExternallyRegisteredService(){
        $discovery = $this->getDiscoveryServiceInstance();
        $names = $discovery->queryForNames();
        $this->assertContains(self::TEST_INTERFACE, $names);
    }

    /**
     * The instance provided by service discovery should utilize remote calls
     * to an external service to satisfy the calculator interface.
     */
    public function testGetInstanceSatisfiedByProxy(){
        $discovery = $this->getDiscoveryServiceInstance();
        $instance = $discovery->getInstance(self::TEST_INTERFACE);
        $this->assertInstanceOf(self::PROXY_CLASS, $instance);
        $this->assertCalculatorInstance($instance);
    }

    /**
     * The instance provided by service discovery should utilize the local
     * instance registered
     */
    public function testGetInstanceSatisfiedByLocal(){
        $discovery = $this->getDiscoveryServiceInstance();
        $instance = $discovery->getInstance(self::TEST_INTERFACE);
        $this->assertNotInstanceOf(self::PROXY_CLASS, $instance);
        $this->assertCalculatorInstance($instance);
    }

    /**
     * Asserts the instance successfully fulfils the contract
     * @param $instance
     */
    protected function assertCalculatorInstance(CalculatorInterface $instance) {
        $a = 3; $b = 7;
        $this->assertTrue($instance->add($a, $b) == $a+$b);
        $this->assertTrue($instance->subtract($a, $b) == $a-$b);
        $this->assertTrue($instance->multiply($a, $b) == $a*$b);
        $this->assertTrue($instance->divide($a, $b) == $a/$b);
    }

    /** @return string absolute path to router file */
    protected function getRouterFile()
    {
        $router = HOME . join(DIRECTORY_SEPARATOR, array('nodes','node-test','index.php'));
        return realpath($router);
    }
}
 