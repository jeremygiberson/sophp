<?php


namespace SOPHP\Core\Proxy\Builder\Exception;

use RuntimeException;

class BuildFailed extends RuntimeException {
    public function __construct($className, \Exception $previous = null)
    {
        parent::__construct("Failed to build proxy for `$className`", 0, $previous);
    }

} 