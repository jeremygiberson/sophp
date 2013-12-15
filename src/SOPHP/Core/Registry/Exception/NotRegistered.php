<?php


namespace SOPHP\Core\Registry\Exception;

use RuntimeException;
use SOPHP\Core\Service\Contract;

class NotRegistered extends RuntimeException {
    public function __construct(Contract $contract)
    {
        parent::__construct("Service `{$contract->getClassName()}` version `{$contract->getVersion()}` not registered.");
    }

} 