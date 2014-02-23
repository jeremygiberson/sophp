<?php


namespace SOPHP\Core\Service\Discovery\Registry\Exception;

use InvalidArgumentException;
use SOPHP\Core\Service\Contract;

class AlreadyRegistered extends InvalidArgumentException {
    public function __construct($interface)
    {
        parent::__construct("{$interface} has already been registered");
    }

} 