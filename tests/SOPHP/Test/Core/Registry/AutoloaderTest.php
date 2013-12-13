<?php


namespace SOPHP\Test\Core\Registry;


use SOPHP\Core\Registry\Autoloader;
use SOPHP\Core\Service\Contract;

class AutoloaderTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testLoadClassFromCloudGeneratesANewClass() {
        $className = uniqid('someClass');
        $contract = new Contract($className);

        $registryMock = $this->getMock('SOPHP\Core\Registry\Registry',
            array('isServiceRegistered', 'getServiceContract'));
        $registryMock->expects($this->once())
            ->method('isServiceRegistered')
            ->with($className)
            ->will($this->returnValue(true));
        $registryMock->expects($this->once())
            ->method('getServiceContract')
            ->with($className)
            ->will($this->returnValue($contract));

        $autoloader = new Autoloader();
        $autoloader->setRegistry($registryMock);
        $result = $autoloader->autoload($className);

        $this->assertEquals($className, $result);

        $this->assertTrue(class_exists($className, false));
    }

}
 