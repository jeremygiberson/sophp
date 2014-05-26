<?php


namespace SOPHP\Framework;


class Version {
    /** @var  int */
    protected $major;
    /** @var  int */
    protected $minor;
    /** @var  int */
    protected $micro;
    /** @var  string */
    protected $quantifier;

    public function __construct($major = 0, $minor = 0, $micro = 0, $quantifier = null){
        $this->setMajor($major);
        $this->setMinor($minor);
        $this->setMicro($micro);
        $this->setQuantifier($quantifier);
    }

    /**
     * @param int $major
     * @return self
     */
    public function setMajor($major)
    {
        $this->major = $major;
        return $this;
    }

    /**
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @param int $micro
     * @return self
     */
    public function setMicro($micro)
    {
        $this->micro = $micro;
        return $this;
    }

    /**
     * @return int
     */
    public function getMicro()
    {
        return $this->micro;
    }

    /**
     * @param int $minor
     * @return self
     */
    public function setMinor($minor)
    {
        $this->minor = $minor;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @param string $quantifier
     * @return self
     */
    public function setQuantifier($quantifier)
    {
        $this->quantifier = $quantifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuantifier()
    {
        return $this->quantifier;
    }

    /**
     * @return string
     */
    public function __toString(){
        return "{$this->getMajor()}.{$this->getMinor()}.{$this->getMicro()}" . ($this->getQuantifier() ?: '');
    }
} 