<?php


namespace SOPHP\Core\Server\Client\Builder;


use Zend\Server\Client;
use Zend\Server\Definition;

interface BuilderInterface {
    /**
     * @param Definition $definition
     * @return Client
     */
    public function build(Definition $definition);
} 