<?php


namespace SOPHP\Core\Service\Builder;


use SOPHP\Core\Service\Service;
use Zend\Uri\Uri;

interface BuilderInterface {
    /**
     * @param Uri $uri
     * @param string $interface
     * @param string|null $concrete
     * @return Service
     */
    public function build(Uri $uri, $interface, $concrete = null);
}