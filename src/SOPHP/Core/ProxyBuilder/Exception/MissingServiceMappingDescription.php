<?php


namespace SOPHP\Core\ProxyBuilder\Exception;

class MissingServiceMappingDescription extends \RuntimeException {
    public function __construct()
    {
        parent::__construct("Smd has not been provided");
    }

} 