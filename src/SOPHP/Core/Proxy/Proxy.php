<?php


namespace SOPHP\Core\Proxy;



use BadMethodCallException;
use SOPHP\Core\Proxy\Exception\MissingRpcClient;
use SOPHP\Zend\Json\Server\Client\ClientAwareInterface;
use Zend\Json\Server\Client;

abstract class Proxy implements ClientAwareInterface {
    /** @var  Client */
    protected $_client;

    /**
     * Return the string to the URI. Must be generated by the builder.
     * @return string
     */
    abstract public function _getUri();

    /**
     * @param Client $client
     * @return self
     */
    public function _setRpcClient(Client $client)
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * Uses lazy instantiation if not available
     * @return Client
     */
    public function _getRpcClient()
    {
        if(!$this->_client) {
            $this->_client = new Client($this->_getUri());
        }
        return $this->_client;
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

        if(!$this->_getRpcClient()) {
            throw new MissingRpcClient();
        }

        print_r($method . ' called with ' . join(', ', $arguments));
        print_r(', will forward to ' . $this->_getUri());
        $result = $this->_getRpcClient()->call($method, $arguments);
        print_r(', and return ' . $result);
        return $result;
    }
}