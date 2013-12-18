<?php


namespace SOPHP\Test\Integration\Core\Proxy;


use SOPHP\Core\Proxy\Builder\ServiceMappingDescription;
use SOPHP\Sample\Calculator\Calculator;
use SOPHP\Test\TestCase\WebServer;
use Zend\Http\Client as HttpClient;
use Zend\Json\Server\Client as JsonClient;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

class ProxyTest extends WebServer {
    public function testHappyPath() {
        $class = 'SOPHP\Sample\Calculator\Calculator';
        // have to fetch smd since we aren't using registry
        $client = new HttpClient();
        $client->setUri($this->getWebServerUri() . "/$class");
        $result = $client->send();

        $smd = new Smd();
        $spec = json_decode($result->getBody(),true);
        $smd->setOptions($spec);
        $smd->setTarget($this->getWebServerUri() . "/$class");

        // build proxy class
        $builder = new ServiceMappingDescription($smd);
        $proxyClass = $builder->build('ProxyCalculator');
        /** @var Calculator $proxy */
        $proxy = new $proxyClass();
        // test it!
        $sum = $proxy->add(3,5);
        $diff = $proxy->subtract(5,3);
        $product = $proxy->multiply(5,3);

        $this->assertEquals(8, $sum);
        $this->assertEquals(2, $diff);
        $this->assertEquals(15, $product);
    }

    /** @return string absolute path to router file */
    protected function getRouterFile()
    {
        $router = HOME . join(DIRECTORY_SEPARATOR, array('nodes','node-test','index.php'));
        return realpath($router);
    }
}
 