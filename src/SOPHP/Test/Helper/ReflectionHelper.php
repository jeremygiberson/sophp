<?php


namespace SOPHP\Test\Helper;


use ReflectionClass;

class ReflectionHelper {
    /**
     * Calls inaccessible $method on $object
     * @param $object
     * @param $method
     * @param array $arguments
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function callProtectedMethod($object, $method, $arguments = array()) {
        if(!is_object($object)) {
            throw new \InvalidArgumentException("\$object must be an object");
        }
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($method);
        if(!$method) {
            throw new \InvalidArgumentException("$method not defined on " . get_class($object));
        }
        $method->setAccessible(true);
        $method->invokeArgs($object, $arguments);
    }
} 