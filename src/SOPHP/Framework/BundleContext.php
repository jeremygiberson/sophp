<?php


namespace SOPHP\Framework;


class BundleContext {
    /** @var  BundleInterface */
    protected $bundle;

    /**
     * @param BundleInterface $bundle
     */
    function __construct($bundle)
    {
        $this->bundle = $bundle;
    }


    /**
     * @param $serviceName
     * @return mixed
     * @throws \Exception
     */
    public function getService($serviceName){
        throw new \Exception("Todo");
    }

    public function getBundle(){
        return $this->bundle;
    }
} 