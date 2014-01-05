<?php


use SOPHP\Core\Service\Builder\Rpc\Json;
use SOPHP\Core\Service\Discovery\Discovery;
use SOPHP\Core\Service\Discovery\Registry\Registry;
use SOPHP\Core\Service\Discovery\Registry\Storage\Memory;
use SOPHP\Core\Service\Provider\Provider;
use SOPHP\Core\Service\Provider\Strategy;
use Zend\Uri\Uri;

class DiscoveryTest extends PHPUnit_Framework_TestCase {
    const TestInterface = '\SOPHP\Sample\Calculator\CalculatorInterface';
    const TestClass = '\SOPHP\Sample\Calculator\Calculator';

    /** @var  Discovery */
    protected $discovery;

    public function setUp() {
        parent::setUp();
        $this->discovery = new Discovery();
        $this->discovery->setRegistry(new Registry());
        $this->discovery->getRegistry()->setStorage(new Memory());
        $this->discovery->setProvider(new Provider());
    }

    public function testRegisterServiceSetsProviderStrategyToLocalForService() {
        $provider = $this->discovery->getProvider();
        $service = $this->getService();
        $test = $provider->getStrategyPreference($service);
        $this->assertEquals(new Strategy(Strategy::Proxy), $test);
        $provider->setStrategyPreference($service, new Strategy(Strategy::Local));
    }

    public function testQueryForNamesReturnsListOfRegisteredInterfaces() {
        $test = $this->discovery->queryForNames();
        $this->assertEmpty($test);

        $this->discovery->registerService(self::TestInterface, $this->getService());
        $test = $this->discovery->queryForNames();
        $this->assertNotEmpty($test);
        $this->assertContains(self::TestInterface, $test);

    }

    /**
     * @expectedException \SOPHP\Core\Service\Discovery\Exception\ServiceNotRegistered
     */
    public function testGetInstanceThrowsServiceNotRegistered() {
        $this->discovery->getInstance('foo');
    }

    /**
     * @return \SOPHP\Core\Service\Service
     */
    protected function getService() {
        $serviceBuilder = new Json();
        return $serviceBuilder->build(new Uri(), self::TestInterface, null);
    }

}
 