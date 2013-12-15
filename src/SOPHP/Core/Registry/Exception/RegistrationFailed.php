<?php


namespace SOPHP\Core\Registry\Exception;


use Exception;
use RuntimeException;

class RegistrationFailed extends RuntimeException {
    public function __construct($message = "Service Registration Failed", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

} 