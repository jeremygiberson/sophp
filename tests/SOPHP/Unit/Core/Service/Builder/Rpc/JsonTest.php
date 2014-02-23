<?php


use SOPHP\Core\Service\Builder\Rpc\Json;
use SOPHP\Sample\Calculator\Calculator;
use Zend\Json\Server\Smd\Service;
use Zend\Uri\Uri;

class JsonTest extends PHPUnit_Framework_TestCase {
    protected $interface;
    protected $concrete;
    protected $concreteMethodThatDoesNotBelongToInterface;

    public function setUp() {
        parent::setUp();
        $this->interface = 'SOPHP\Sample\Calculator\CalculatorInterface';
        $this->concrete = new Calculator();
        $this->concreteMethodThatDoesNotBelongToInterface = 'notAnExposedMethod';
    }

    public function testBuildReturnsDefinitionForInterface() {
        $interfaceMethods = $this->getClassMethodNames($this->interface);
        $concreteMethods = $this->getClassMethodNames($this->concrete);

        $serviceNames = array();
        $json = new Json($this->interface, $this->concrete);
        $service = $json->build(new Uri(), $this->interface, $this->concrete);
        $definition = $service->getDefinition();
        foreach($definition->getMethods() as $method) {
            /** @var $method Service */
            $name = $method->getName();
            $this->assertContains($name, $interfaceMethods);
            $this->assertContains($name, $concreteMethods);
            $serviceNames[] = $name;
        }
        $this->assertContains($this->concreteMethodThatDoesNotBelongToInterface, $concreteMethods);
        $this->assertNotContains($this->concreteMethodThatDoesNotBelongToInterface, $interfaceMethods);
        $this->assertNotContains($this->concreteMethodThatDoesNotBelongToInterface, $serviceNames);
    }

    /**
     * @param $className
     * @return array
     */
    protected function getClassMethodNames($className) {
        $reflectionClass = new ReflectionClass($className);
        $names = array();
        foreach($reflectionClass->getMethods() as $method) {
            $names[] = $method->getName();
        }
        return $names;
    }
}
 