<?php


namespace SOPHP\Core\Proxy\Builder;
use SOPHP\Core\Proxy\Builder\Exception\BuildFailed;
use SOPHP\Core\Proxy\Builder\Exception\MissingServiceMappingDescription;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Json\Server\Smd;
use Zend\Json\Server\Smd\Service as SmdService;

/**
 * Class ServiceMappingDescription
 * Builds a Proxy class from a supplied Smd
 * @package SOPHP\Core\ProxyBuilder
 */
class ServiceMappingDescription implements BuilderInterface {
    /** @var  Smd */
    protected $smd;

    /**
     * @param \Zend\Json\Server\Smd $smd
     */
    public function setSmd($smd)
    {
        $this->smd = $smd;
    }

    /**
     * @return \Zend\Json\Server\Smd
     */
    public function getSmd()
    {
        return $this->smd;
    }

    /**
     * @param Smd $smd
     */
    function __construct(Smd $smd = null)
    {
        $this->setSmd($smd);
    }


    /**
     * After build is called, the class name returned by the function should exist in scope
     *
     * @param string $className
     * @throws Exception\MissingServiceMappingDescription
     * @throws Exception\BuildFailed
     * @return string get_class of the generated proxy
     */
    public function build($className)
    {
        if(!$this->getSmd()) {
            throw new MissingServiceMappingDescription();
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
        foreach($this->smd->getServices() as $service) {
            $this->generateMethod($service, $generator);
        }
    }

    /**
     * @param SmdService $service
     * @param $generator
     */
    protected function generateMethod(SmdService $service, ClassGenerator &$generator)
    {
        $method = new MethodGenerator($service->getName());
        foreach($service->getParams() as $position => $param) {
            $this->generateParam($position, $param, $method);
        }

        $method->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);

        $method->setBody('return $this->internalCallHandler(__FUNCTION__,func_get_args());');

        $generator->addMethodFromGenerator($method);
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
        $uri = addslashes($this->getSmd()->getTarget());
        $method->setBody("return \"$uri\";");
        $generator->addMethodFromGenerator($method);
    }
}