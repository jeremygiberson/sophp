<?php


namespace SOPHP\Zend\Json\Server\Client;


use Zend\Json\Server\Client;

interface ClientAwareInterface {
    /**
     * @param Client $client
     * @return mixed
     */
    public function _setRpcClient(Client $client);

    /**
     * @return Client
     */
    public function _getRpcClient();
} 