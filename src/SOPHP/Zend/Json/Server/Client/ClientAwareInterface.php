<?php


namespace SOPHP\Zend\Json\Server\Client;


use Zend\Json\Server\Client;

interface ClientAwareInterface {
    /**
     * @param Client $client
     * @return mixed
     */
    public function setRpcClient(Client $client);

    /**
     * @return Client
     */
    public function getRpcClient();
} 