<?php


namespace SOPHP\Core\ProxyBuilder;


interface ProxyBuilderInterface {
    /**
     * After build is called, the class name returned by the function should exist in scope
     * @param string $className
     * @return string get_class of the generated proxy
     */
    public function build($className);
} 