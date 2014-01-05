<?php


namespace SOPHP\Core\Service\Builder\Rpc;


use ReflectionClass;
use SOPHP\Core\Service\Adapter\BuilderInterface;
use SOPHP\Core\Service\Service;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;
use Zend\Server\Definition;
use Zend\Server\Reflection\AbstractFunction;
use Zend\Server\Reflection\ReflectionClass as JsonReflectionClass;
use Zend\Uri\Uri;

class Json implements BuilderInterface {
    const SERVER_BUILDER = '\SOPHP\Core\Server\Builder\Json';
    const CLIENT_BUILDER = '\SOPHP\Core\Server\Client\Builder\Json';

    /**
     * @param Uri $uri
     * @param string $interface
     * @param mixed $concrete
     * @throws \InvalidArgumentException
     * @return \SOPHP\Core\Service\Service|\Zend\Server\Definition
     */
    public function build(Uri $uri, $interface, $concrete = null) {
        if(!interface_exists($interface)) {
            throw new \InvalidArgumentException("`{$interface}` must be an interface");
        }

        if($concrete != null && !is_a($concrete, $interface)) {
            throw new \InvalidArgumentException(get_class($concrete) . " must implement `{$interface}`");
        }

        $reflection = new ReflectionClass($interface);

        $server = $this->addMethodsToServiceMap($reflection, $concrete);
        $definitions = $server->getFunctions();
        return $this->createService($interface, $uri, $definitions);
    }

    /**
     * @param Server $server
     * @param AbstractFunction $method
     * @param mixed $concrete
     * @return Server
     */
    protected function addMethodToServiceMap(Server &$server, AbstractFunction $method, $concrete = null)
    {
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
     * @return Server
     */
    protected function addMethodsToServiceMap(ReflectionClass $reflection, $concrete = null)
    {
        $server = new Server();
        // using Json server
        $reflection = new JsonReflectionClass($reflection);
        foreach ($reflection->getMethods() as $method) {
            $this->addMethodToServiceMap($server, $method, $concrete);
        }
        return $server;
    }

    /**
     * @param string $interface
     * @param Uri $uri
     * @param Definition $definitions
     * @return Service
     */
    protected function createService($interface, Uri $uri, Definition $definitions)
    {
        $service = new Service();
        $service->setInterface($interface);
        $service->setUri($uri);
        $service->setDefinition($definitions);
        $service->setServerBuilderClass(self::SERVER_BUILDER);
        $service->setClientBuilderClass(self::CLIENT_BUILDER);
        return $service;
    }
}