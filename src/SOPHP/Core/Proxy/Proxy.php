<?php


namespace SOPHP\Core\Proxy;



use BadMethodCallException;
use SOPHP\Core\Proxy\Exception\MissingRpcClient;
use SOPHP\Zend\Json\Server\Client\ClientAwareInterface;
use Zend\Json\Server\Client;

class Proxy implements ClientAwareInterface {
    /** @var  Client */
    protected $client;

    /**
     * @param Client $client
     * @return self
     */
    public function setRpcClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client
     */
    public function getRpcClient()
    {
        return $this->client;
    }

    /**
     * Proxy Magic!
     * usage: return $this->internalCallHandler(__FUNCTION__,func_get_args())
     * @param $method
     * @param $arguments
     * @throws MissingRpcClient
     * @throws \BadMethodCallException
     * @return string
     */
    protected function internalCallHandler($method, $arguments) {
        if(!method_exists($this, $method)) {
            throw new BadMethodCallException();
        }

        if(!$this->getRpcClient()) {
            throw new MissingRpcClient();
        }

        $result = $this->getRpcClient()->call($method, $arguments);
        print_r($method . ' called with ' . join(', ', $arguments) . ', returning ' . $result);
        return $result;
    }
}