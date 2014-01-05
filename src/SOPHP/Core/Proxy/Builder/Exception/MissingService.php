<?php


namespace SOPHP\Core\Proxy\Builder\Exception;


class MissingService extends \RuntimeException {
    public function __construct()
    {
        parent::__construct("Service has not been provided");
    }
} 