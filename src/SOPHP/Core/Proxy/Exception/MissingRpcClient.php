<?php


namespace SOPHP\Core\Proxy\Exception;
;
use RuntimeException;

class MissingRpcClient extends RuntimeException {
    public function __construct()
    {
        parent::__construct("Rpc client has not been provided");
    }

} 