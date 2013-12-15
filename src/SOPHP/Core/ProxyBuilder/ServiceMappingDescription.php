<?php


namespace SOPHP\Core\ProxyBuilder;
use SOPHP\Core\ProxyBuilder\Exception\MissingServiceMappingDescription;
use Zend\Code\Generator\ClassGenerator;
use Zend\Json\Server\Smd;
/**
 * Class ServiceMappingDescription
 * Builds a Proxy class from a supplied Smd
 * @package SOPHP\Core\ProxyBuilder
 */
class ServiceMappingDescription implements ProxyBuilderInterface {
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
    function __construct(Smd $smd)
    {
        $this->setSmd($smd);
    }


    /**
     * After build is called, the class name returned by the function should exist in scope
     * @throws Exception\MissingServiceMappingDescription
     * @param string $className
     * @return string get_class of the generated proxy
     */
    public function build($className)
    {
        if(!$this->getSmd()) {
            throw new MissingServiceMappingDescription();
        }

        $generator = new ClassGenerator();
        $generator->setName($className);
        // todo: generate functional proxy class based on service mapping description of contract
        $source = $generator->generate();
        eval($source);
        return $className;
    }
}