<?php


namespace SOPHP\Core\Registry\Exception;

use InvalidArgumentException;
use SOPHP\Core\Service\Contract;

class AlreadyRegistered extends InvalidArgumentException {
    public function __construct(Contract $contract)
    {
        parent::__construct("{$contract->getClassName()} version {$contract->getVersion()} has already been registered");
    }

} 