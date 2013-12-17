<?php


use SOPHP\Core\Proxy\Builder\ServiceMappingDescription;
use SOPHP\Core\Proxy\Proxy;
use SOPHP\Sample\Calculator\Calculator;
use SOPHP\Test\Helper\ReflectionHelper;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;
use Zend\Json\Server\Smd\Service;

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

    /**
     * @expectedException \SOPHP\Core\Proxy\Builder\Exception\MissingServiceMappingDescription
     */
    public function testSmdRequiredForBuild() {
        $builder = new ServiceMappingDescription();
        $builder->build('foo');
    }

    public function testGenerateParamSetsType() {
        $paramName = 'foo';
        $type = 'float';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
                'type' => $type
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        $this->assertEquals($type, $parameter->getType());
    }

    public function testGenerateParamSetsMixedTypeIfTypeHintContainsMultipleTypes() {
        $paramName = 'foo';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
                'type' => array('int','float')
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        $this->assertEquals('mixed', $parameter->getType());
    }

    public function testGenerateParamSetsDefaultValueIfOptional() {
        $paramName = 'foo';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
                'optional' => true,
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        /** @var \Zend\Code\Generator\ValueGenerator $default */
        $default = $parameter->getDefaultValue();
        $this->assertNull($default->getValue());
    }

    public function testGenerateParamSetsDefaultValueIfDefaultValueProvided() {
        $paramName = 'foo';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        $value = uniqid();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
                'default' => $value,
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        /** @var \Zend\Code\Generator\ValueGenerator $default */
        $default = $parameter->getDefaultValue();
        $this->assertEquals($value, $default->getValue());
    }

    public function testGenerateParamSetsDefaultValueIfDefaultValueAndOptionalSet() {
        $paramName = 'foo';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        $value = uniqid();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
                'default' => $value,
                'optional' => true,
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        /** @var \Zend\Code\Generator\ValueGenerator $default */
        $default = $parameter->getDefaultValue();
        $this->assertEquals($value, $default->getValue());
    }

    public function testGenerateParamDoesNotSetDefaultValue() {
        $paramName = 'foo';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        $value = uniqid();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        /** @var \Zend\Code\Generator\ValueGenerator $default */
        $this->assertNull($parameter->getDefaultValue());
    }

    public function testGenerateParamDoesNotSetType() {
        $paramName = 'foo';
        $methodGenerator = new MethodGenerator();
        $builder = new ServiceMappingDescription();
        $value = uniqid();
        ReflectionHelper::callProtectedMethod($builder, 'generateParam', $params = array(
            0,
            array(
                'name' => $paramName,
            ),
            &$methodGenerator
        ));
        $parameters = $methodGenerator->getParameters();
        $this->assertCount(1, $parameters);
        /** @var ParameterGenerator $parameter */
        $parameter = $parameters[$paramName];
        $this->assertEquals($paramName, $parameter->getName());
        /** @var \Zend\Code\Generator\ValueGenerator $default */
        $this->assertNull($parameter->getType());
    }

    public function testGenerateMethodAddsAllParams() {
        $method = 'foo';
        $paramCount = rand(2,7);
        $paramNames = array();
        $smdService = new Service($method);

        for($i = 0; $i < $paramCount; $i++) {
            $name = "param$i";
            $smdService->addParam('mixed', array('name'=>$name));
            $paramNames[] = $name;
        }

        $generator = new ClassGenerator();

        $builder = new ServiceMappingDescription();
        ReflectionHelper::callProtectedMethod($builder, 'generateMethod', $params = array(
            $smdService,
            &$generator
        ));

        $methodGenerator = $generator->getMethod($method);
        $params = $methodGenerator->getParameters();
        $this->assertCount($paramCount, $params);
        foreach($params as $param) {
            /** @var ParameterGenerator $param */
            $this->assertContains($param->getName(), $paramNames);
        }
    }

    public function testGenerateMethodsAddsAllMethods() {
        $method = 'foo';
        $methodCount = rand(2,7);
        $methodNames = array();
        $smd = new Smd();

        for($i = 0; $i < $methodCount; $i++) {
            $name = "method$i";
            $smdService = new Service($name);
            $smd->addService($smdService);
            $methodNames[] = $name;
        }

        $generator = new ClassGenerator();

        $builder = new ServiceMappingDescription();
        $builder->setSmd($smd);
        ReflectionHelper::callProtectedMethod($builder, 'generateMethods', $params = array(
            &$generator
        ));

        $methods = $generator->getMethods();
        $this->assertCount($methodCount, $methods);

        foreach($methods as $methodGenerator) {
            /** @var MethodGenerator $param */
            $this->assertContains($methodGenerator->getName(), $methodNames);
        }
    }

    /**
     * @expectedException \SOPHP\Core\Proxy\Builder\Exception\BuildFailed
     */
    public function testBuildThrowsBuildFailedIfClassDoesNotExist() {
        $builder = new ServiceMappingDescription();
        $builder->setSmd(new Smd());
        $builder->build('123');
    }
}
 