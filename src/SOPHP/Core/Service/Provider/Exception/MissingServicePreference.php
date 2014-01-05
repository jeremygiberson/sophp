<?php


namespace SOPHP\Core\Service\Provider\Exception;


use SOPHP\Core\Service\Service;

class MissingServicePreference extends \RuntimeException{
    public function __construct(Service $service)
    {
        $message = "Service Locator does not know how to provide an instance for "
            . $service->getInterface();
        parent::__construct($message);
    }

} 