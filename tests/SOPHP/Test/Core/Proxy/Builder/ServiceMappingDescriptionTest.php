<?php


use SOPHP\Core\Proxy\Builder\ServiceMappingDescription;
use SOPHP\Core\Proxy\Proxy;
use SOPHP\Sample\Calculator\Calculator;
use Zend\Json\Server\Server;

class ServiceMappingDescriptionTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testHappyPath() {
        $server = new Server();
        $server->setClass('SOPHP\Sample\Calculator\Calculator');
        $uri = 'http://foobar';
        $smd = $server->getServiceMap();
        $smd->setTarget($uri);
        $builder = new ServiceMappingDescription($smd);

        $class = 'MyCalculator';
        $test = $builder->build($class);

        $this->assertEquals($class, $test);
        $this->assertTrue(class_exists($class, false));

        $a = 7; $b = 11; $c = $a+$b;

        $rpcMock = $this->getMock('Zend\Json\Server\Client', array(), array(), '', false);
        $rpcMock->expects($this->once())
            ->method('call')
            ->with('add', array($a, $b))
            ->will($this->returnValue($c));

        /** @var Proxy $proxy */
        $proxy = new $class();
        $proxy->_setRpcClient($rpcMock);
        $this->assertEquals($uri, $proxy->_getUri());
        /** @var Calculator $proxy */
        $sum = $proxy->add($a, $b);
        $this->assertEquals($c, $sum);
    }

    public function testSmdRequiredForBuild() {
        $this->markTestIncomplete('todo');
    }

    public function testProductClassNameMatchesParameter() {
        $this->markTestIncomplete('todo');
    }

    public function testProductMethodParameterOptional() {
        $this->markTestIncomplete('todo');
    }

    public function testProductMethodParameterDefaultValue() {
        $this->markTestIncomplete('todo');
    }

    public function testProductMethodParameterNameProvided() {
        $this->markTestIncomplete('todo');
    }

    public function testProductMethodParameterNameCreated() {
        $this->markTestIncomplete('todo');
    }

    public function testProductContainsExpectedMethods() {
        $this->markTestIncomplete('todo');
    }

    public function testProductMethodParametersMatchExpected() {
        $this->markTestIncomplete('todo');
    }

    public function testProductReturnMatchExpected() {
        $this->markTestIncomplete('todo');
    }
}
 