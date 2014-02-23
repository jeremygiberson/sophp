<?php


namespace SOPHP\Core\Service\Discovery\Registry\Exception;

use RuntimeException;
use SOPHP\Core\Service\Contract;

class NotRegistered extends RuntimeException {
    public function __construct($interface)
    {
        parent::__construct("Service `{$interface}` not registered.");
    }

} 