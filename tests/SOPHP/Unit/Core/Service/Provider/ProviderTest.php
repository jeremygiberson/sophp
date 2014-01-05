<?php


use SOPHP\Core\Service\Builder\Rpc\Json;
use SOPHP\Core\Service\Provider\Provider;
use SOPHP\Core\Service\Provider\Strategy;
use Zend\Uri\Uri;

class ProviderTest extends PHPUnit_Framework_TestCase {
    /** @var  Provider */
    protected $provider;
    const TestInterface = '\SOPHP\Sample\Calculator\CalculatorInterface';
    const TestClass = '\SOPHP\Sample\Calculator\Calculator';
    const ProxyClass = '\SOPHP\Core\Proxy\Proxy';



    public function setUp() {
        parent::setUp();
        $this->provider = new Provider();
    }

    public function testGetStrategyPreferencesReturnsLazyInitializationInstance(){
        $test = $this->provider->getStrategyPreferences();
        $this->assertInstanceOf('SplObjectPreference', $test);
    }

    public function testGetStrategyPreferencesReturnsExistingInstance() {
        $obj = new stdClass();
        $preferences = new SplObjectStorage();
        $preferences->attach($obj);

        $this->provider->setStrategyPreferences($preferences);
        $test = $this->provider->getStrategyPreferences();
        $this->assertEquals($preferences, $test);
        $this->assertTrue($test->contains($obj));
    }

    public function testGetInstanceReturnsLocalImplementation(){
        $service = $this->getService();
        $this->provider->setStrategyPreference($service,new Strategy(Strategy::Local));
        $test = $this->provider->getInstance($service);
        $this->assertTrue(is_a($test, self::TestInterface));
        $this->assertTrue(is_a($test, self::TestClass));
    }

    public function testGetInstanceReturnsProxyImplementation(){
        $service = $this->getService();
        $this->provider->setStrategyPreference($service,new Strategy(Strategy::Proxy));
        $test = $this->provider->getInstance($service);
        $this->assertFalse(is_a($test, self::TestClass));
        $this->assertTrue(is_a($test, self::TestInterface));
        $this->assertTrue(is_a($test, self::ProxyClass));
    }

    /**
     * @return \SOPHP\Core\Service\Service
     */
    protected function getService() {
        $serviceBuilder = new Json();
        return $serviceBuilder->build(new Uri(), self::TestInterface, null);
    }
}
 