<?php


namespace SOPHP\Core\Service\Discovery\Rpc;


use SOPHP\Core\Proxy\Builder\BuilderInterface;

interface Adapter {
    /** @return mixed */
    public function getConcrete();

    /**
     * @param mixed $concrete
     * @return Adapter
     */
    public function setConcrete($concrete);

    /** @return mixed */
    public function getInterface();

    /**
     * @param mixed $interface
     * @return Adapter
     */
    public function setInterface($interface);

    /**
     * @return mixed
     */
    public function getServiceMap();

    /**
     * @return BuilderInterface
     */
    public function getProxyBuilder();
} 