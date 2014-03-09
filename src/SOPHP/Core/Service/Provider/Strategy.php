<?php


namespace SOPHP\Core\Service\Provider;


class Strategy /* extends \SplEnum */ {
    const __default = self::Local;
    const Local = 1;
    const Proxy = 2;

    protected $value;

    public function __construct($value = self::__default) {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue(){
        return $this->value;
    }
} 