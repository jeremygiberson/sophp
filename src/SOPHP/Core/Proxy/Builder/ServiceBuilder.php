<?php


namespace SOPHP\Core\Proxy\Builder;

use SOPHP\Core\Proxy\Builder\Exception\BuildFailed;
use SOPHP\Core\Proxy\Builder\Exception\MissingService;
use SOPHP\Core\Service\Service;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Server\Definition;

class ServiceBuilder implements BuilderInterface {
    /** @var  Service */
    protected $service;

    public function __construct(Service $service) {
        $this->setService($service);
    }

    /**
     * @param \SOPHP\Core\Service\Service $service
     * @return self
     */
    public function setService(Service $service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return \SOPHP\Core\Service\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return Definition
     */
    protected function getDefinition() {
        return $this->getService()->getDefinition();
    }


    /**
     * After build is called, the class name returned by the function should exist in scope
     * @param string $className
     * @throws Exception\BuildFailed
     * @throws Exception\MissingService
     * @return string get_class of the generated proxy
     */
    public function build($className)
    {
        if(!$this->getDefinition()) {
            throw new MissingService();
        }

        $generator = new ClassGenerator();
        $generator->setName($className);

        $this->extendClass($generator);

        $this->generateMethods($generator);

        $this->generateUriMethod($generator);

        $source = $generator->generate();

        try {
            eval($source);
        } catch (\Exception $e) {
            throw new BuildFailed($className,$e);
        }

        if(!class_exists($className,false)) {
            throw new BuildFailed($className);
        }
        return $className;
    }

    /**
     * @param $generator
     */
    protected function generateMethods(ClassGenerator &$generator)
    {
        foreach($this->getDefinition()->getMethods() as $method) {
            $this->generateMethod($method, $generator);
        }
    }

    /**
     * @param Definition $method
     * @param $generator
     */
    protected function generateMethod(Definition $method, ClassGenerator &$generator)
    {
        $methodGenerator = new MethodGenerator($method->getName());
        foreach($method->getParams() as $position => $param) {
            $this->generateParam($position, $param, $methodGenerator);
        }

        $methodGenerator->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);

        $methodGenerator->setBody('return $this->internalCallHandler(__FUNCTION__,func_get_args());');

        $generator->addMethodFromGenerator($methodGenerator);
    }

    /**
     * @param $position
     * @param array $param
     * @param MethodGenerator $method
     */
    protected function generateParam($position, array $param, MethodGenerator &$method)
    {
        $parameter = new ParameterGenerator();
        $parameter->setPosition($position);
        $parameter->setName(@$param['name'] ?: 'param'.$position);
        if(isset($param['type']) && is_array($param['type'])) {
            // todo docblock join w/ |
            $parameter->setType('mixed');
        } else if (isset($param['type'])) {
            $parameter->setType($param['type']);
        }
        if(isset($param['default'])) {
            $parameter->setDefaultValue($param['default']);
        } else if(isset($param['optional']) && $param['optional']) {
            $parameter->setDefaultValue(null);
        }

        $method->setParameter($parameter);
    }

    /**
     * Do any extra logic regarding extending the class & added interfaces
     * @param ClassGenerator $generator
     */
    protected function extendClass(ClassGenerator &$generator)
    {
        $generator->setExtendedClass('SOPHP\Core\Proxy\Proxy');
    }

    protected function generateUriMethod(ClassGenerator &$generator)
    {
        $method = new MethodGenerator('_getUri', array(), MethodGenerator::FLAG_PUBLIC);
        $uri = addslashes($this->getService()->getUri()->toString());
        $method->setBody("return \"$uri\";");
        $generator->addMethodFromGenerator($method);
    }
}