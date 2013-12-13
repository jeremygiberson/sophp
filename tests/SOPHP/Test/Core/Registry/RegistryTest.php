<?php


namespace SOPHP\Test\Core\Registry;

use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

class RegistryTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testRegisterServiceCasLoopWhenNothingInStorage() {
        $this->markTestIncomplete('todo');
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
 