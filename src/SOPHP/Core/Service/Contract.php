<?php


namespace SOPHP\Core\Service;


use Zend\Json\Server\Smd;

class Contract {
    /**
     * @var string
     */
    protected $className;
    /** @var  string */
    protected $md5;
    /** @var  Smd */
    protected $smd;
    /** @var  string */
    protected $version;


    public function __construct($className = null, Smd $smd = null, $version = null){
        $this->setClassName($className);
        $this->setSmd($smd);
        $this->setVersion($version);
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $md5
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
    }

    /**
     * @return string
     */
    public function getMd5()
    {
        return $this->md5;
    }

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
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * generate md5 based on attributes
     */
    public function calculateMd5()
    {
        return md5($this->className . ($this->smd ? $this->smd->toJson() : '{}') . $this->version);
    }

} 