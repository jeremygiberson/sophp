<?php


namespace SOPHP\Core\Proxy\Builder\Exception;

use RuntimeException;

class BuildFailed extends RuntimeException {
    public function __construct($className)
    {
        parent::__construct("Failed to build proxy for `$className`");
    }

} 