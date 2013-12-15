<?php


namespace SOPHP\Core\Proxy\Builder\Exception;

class MissingServiceMappingDescription extends \RuntimeException {
    public function __construct()
    {
        parent::__construct("Smd has not been provided");
    }

} 