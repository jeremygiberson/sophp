<?php


namespace SOPHP\Core\Server\Builder;


use Zend\Server\AbstractServer;
use Zend\Server\Definition;

interface BuilderInterface {
    /**
     * @param Definition $definition
     * @return AbstractServer
     */
    public function build(Definition $definition);
} 