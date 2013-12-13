<?php


namespace SOPHP\Core\Service;


class Contract {
    /**
     * @var string
     */
    protected $className;


    public function __construct($className = null){
        $this->setClassName($className);
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

} 