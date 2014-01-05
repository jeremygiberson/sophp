<?php


namespace SOPHP\Core\Service;
use \Zend\Server\Definition;
use Zend\Uri\Uri;
use SOPHP\Core\Server\Builder\BuilderInterface;
use SOPHP\Core\Server\Client\Builder\BuilderInterface as ClientBuilder;

class Service implements \Serializable {
    const SERIALIZE_KEY_URI = 'uri';
    const SERIALIZE_KEY_CLIENT = 'client';
    const SERIALIZE_KEY_SERVER = 'server';
    const SERIALIZE_KEY_DEFINITION = 'definition';

    /** @var  Definition */
    protected $definition;
    /** @var  Uri */
    protected $uri;
    /** @var  string */
    protected $interface;
    /** @var  string */
    protected $serverBuilderClass;
    /** @var  string */
    protected $clientBuilderClass;

    /**
     * @param \Zend\Server\Definition $definition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }

    /**
     * @return \Zend\Server\Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param $clientBuilderClass
     * @throws \InvalidArgumentException
     */
    public function setClientBuilderClass($clientBuilderClass)
    {
        if(!class_exists($clientBuilderClass)) {
            throw new \InvalidArgumentException("`$clientBuilderClass` is not a valid class");
        }
        if($clientBuilderClass instanceof ClientBuilder) {
            throw new \InvalidArgumentException("`$clientBuilderClass` must implement Client\\BuilderInterface");
        }
        $this->clientBuilderClass = $clientBuilderClass;
    }

    /**
     * @return string
     */
    public function getClientBuilderClass()
    {
        return $this->clientBuilderClass;
    }



    /**
     * @param string $serverBuilderClass
     * @throws \InvalidArgumentException
     */
    public function setServerBuilderClass($serverBuilderClass)
    {
        if(!class_exists($serverBuilderClass)) {
            throw new \InvalidArgumentException("`$serverBuilderClass` is not a valid class");
        }
        if($serverBuilderClass instanceof BuilderInterface) {
            throw new \InvalidArgumentException("`$serverBuilderClass` must implement BuilderInterface");
        }
        $this->serverBuilderClass = $serverBuilderClass;
    }

    /**
     * @return string
     */
    public function getServerBuilderClass()
    {
        return $this->serverBuilderClass;
    }

    /**
     * @param \Zend\Uri\Uri $uri
     */
    public function setUri(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return \Zend\Uri\Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $interface
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
    }

    /**
     * @return string
     */
    public function getInterface()
    {
        return $this->interface;
    }



    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            self::SERIALIZE_KEY_URI => $this->uri->toString(),
            self::SERIALIZE_KEY_CLIENT => $this->clientBuilderClass,
            self::SERIALIZE_KEY_SERVER => $this->serverBuilderClass,
            self::SERIALIZE_KEY_DEFINITION => $this->definition->toArray()
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        $this->setUri(new Uri($array[self::SERIALIZE_KEY_URI]));
        $this->setClientBuilderClass($array[self::SERIALIZE_KEY_CLIENT]);
        $this->setServerBuilderClass($array[self::SERIALIZE_KEY_SERVER]);
        $this->setDefinition(new Definition($array[self::SERIALIZE_KEY_DEFINITION]));
    }
}