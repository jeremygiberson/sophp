<?php


namespace SOPHP\Test\Integration\Core\Proxy;


use SOPHP\Test\TestCase\WebServer;
use Zend\Http\Client;
use Zend\Json\Server\Server;

class ProxyTest extends WebServer {
    public function testHappyPath() {
        $class = 'SOPHP\Sample\Calculator\Calculator';
        // setup rpc server
        $jsonServer = new Server();
        $jsonServer->setClass($class);
        $client = new Client();
        $client->setUri('http://'
            . self::getWebServerHost() .':'. self::getWebServerPort()
            . "/$class");
        $result = $client->send();
        var_dump($result->getBody());
    }

    /** @return string absolute path to router file */
    protected function getRouterFile()
    {
        $router = HOME . join(DIRECTORY_SEPARATOR, array('nodes','node-test','index.php'));
        return realpath($router);
    }
}
 