<?php


namespace SOPHP\Core\Service\Provider;


class Strategy extends \SplEnum {
    const __default = self::Local;
    const Local = 1;
    const Proxy = 2;
} 