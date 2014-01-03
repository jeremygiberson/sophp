<?php


namespace SOPHP\Core\Service\Discovery\Rpc;


use ReflectionClass;
use SOPHP\Core\Proxy\Builder\BuilderInterface;
use SOPHP\Core\Proxy\Builder\ServiceMappingDescription;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;
use Zend\Server\Reflection\AbstractFunction;
use Zend\Server\Reflection\ReflectionClass as JsonReflectionClass;

class Json implements Adapter {
    /** @var  Server */
    protected $server;
    /** @var  SMD */
    protected $serviceMap;
    /** @var  mixed */
    protected $interface;
    /** @var  mixed */
    protected $concrete;

    /**
     * @param mixed $interface
     * @param mixed $concrete
     */
    public function __construct($interface, $concrete) {
        $this->setInterface($interface);
        $this->setConcrete($concrete);
    }

    /**
     * @return Server
     */
    public function getServer() {
        if($this->server === null) {
            $this->server = new Server();
        }
        return $this->server;
    }

    /**
     * @param Server $server
     */
    public function setServer(Server $server) {
        $this->server = $server;
    }

    /**
     * @param mixed $concrete
     * @return Adapter
     */
    public function setConcrete($concrete)
    {
        $this->concrete = $concrete;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * @param mixed $interface
     * @return Adapter
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * @return \Zend\Json\Server\Smd
     * @throws \InvalidArgumentException
     */
    public function getServiceMap() {
        if($this->serviceMap === null) {

            if(!interface_exists($this->interface)) {
                throw new \InvalidArgumentException("`{$this->interface}` must be an interface");
            }

            if(!is_a($this->concrete, $this->interface)) {
                throw new \InvalidArgumentException(get_class($this->concrete) . " must implement `{$this->interface}`");
            }

            $reflection = new ReflectionClass($this->interface);

            $this->addMethodsToServiceMap($reflection, $this->concrete);

            $this->serviceMap = $this->getServer()->getServiceMap();
        }
        return $this->serviceMap;
    }

    /**
     * @param AbstractFunction $method
     * @param mixed $concrete
     */
    protected function addMethodToServiceMap(AbstractFunction $method, $concrete)
    {
        $server = $this->getServer();
        $serverReflection = new ReflectionClass($server);
        $reflectionMethod = $serverReflection->getMethod('_buildSignature');
        $reflectionMethod->setAccessible(true);
        $definition = $reflectionMethod->invoke($server, $method, $concrete);


        $reflectionMethod = $serverReflection->getMethod('_addMethodServiceMap');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($server, $definition);
    }

    /**
     * @param ReflectionClass $reflection
     * @param mixed $concrete
     */
    protected function addMethodsToServiceMap(ReflectionClass $reflection, $concrete)
    {
        // using Json server
        $reflection = new JsonReflectionClass($reflection);
        foreach ($reflection->getMethods() as $method) {
            $this->addMethodToServiceMap($method, $concrete);
        }
    }

    /**
     * @return BuilderInterface
     */
    public function getProxyBuilder()
    {
        return new ServiceMappingDescription($this->getServiceMap());
    }


}