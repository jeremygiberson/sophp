<?php


namespace SOPHP\Zend\Cache\Storage;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Traversable;
use Zend\Cache\Storage\Adapter;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\StorageInterface;

/**
 * Class Mock
 * @package SOPHP\Zend\Cache\Storage
 * For unit testing
 */
class StorageMock implements StorageInterface {

    protected $will = array();
    /** @var \PHPUnit_Framework_TestCase  */
    protected $testCase;
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Cache\Storage\StorageInterface */
    public $mock;

    public function __construct(PHPUnit_Framework_TestCase $testCase) {
        if(!$testCase) {
            throw new \InvalidArgumentException("PHPUnit test case must be provided");
        }

        $this->testCase = $testCase;
        /** @var PHPUnit_Framework_MockObject_MockObject mock */
        $this->mock = $testCase->getMock('Zend\Cache\Storage\StorageInterface');
    }

    /**
     * Describe what will happen every time the method is called. You can specify the return
     * value, and the values the parameters are set to (for pass by reference behavior)
     *
     * @param $methodName
     * @param $returnValue
     * @param $paramOneValue
     * @param $paramNValue (repeatable)
     */
    public function setMethodWill($methodName, $returnValue, $paramOneValue = null, $paramNValue = null) {
        $args = func_get_args();
        array_shift($args);
        $this->will[$methodName] = (object)array(
            'returnValue' => $returnValue,
            'paramValues' => $args
        );
    }

    /**
     * Describe what will happen for a single execution of the method. add operates as a stack,
     * first in, first out. You can specify the return values, and the values the parameters
     * are set to (for pass by reference behaviour)
     * @param $methodName
     * @param $returnValue
     * @param $paramOneValue
     * @param $paramNValue
     */
    public function addMethodWill($methodName, $returnValue, $paramOneValue = null, $paramNValue = null) {
        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        if(!isset($this->will[$methodName]) || !is_array($this->will[$methodName])){
            $this->will[$methodName] = array();
        }

        $this->will[$methodName][] =(object)array(
            'returnValue' => $returnValue,
            'paramValues' => $args
        );
    }

    /**
     * reset mock
     */
    public function reset() {
        $this->will = array();
    }


    /**
     * Calls the method on the mock. If no will's have been specified, returns mock value
     * otherwise returns the will's returnValue
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    protected function handleMethodCall($methodName, &$arguments = array()) {
        $result = call_user_func_array(array($this->mock, $methodName), &$arguments);

        if(!isset($this->will[$methodName])) {
            return $result;
        }
        $willSource = $this->will[$methodName];
        if(empty($willSource)) {
            return $result;
        } else if(is_array($willSource)) {
            $will = array_shift($willSource);
        } else {
            $will = $willSource;
        }

        foreach($will->paramValues as $n => $value) {
            if(array_key_exists($n, $arguments)) {
                $arguments[$n] = $value;
            }
        }
        return $will->returnValue;
    }

    /**
     * Set options.
     *
     * @param array|Traversable|Adapter\AdapterOptions $options
     * @return StorageInterface Fluent interface
     */
    public function setOptions($options)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, &$args);
    }

    /**
     * Get options
     *
     * @return Adapter\AdapterOptions
     */
    public function getOptions()
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__,&$args);
    }

    /**
     * Get an item.
     *
     * @param  string $key
     * @param  bool $success
     * @param  mixed $casToken
     * @return mixed Data on success, null on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $args = array($key, &$success, &$casToken);
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @return array Associative array of keys and values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItems(array $keys)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItem($key)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Test multiple items.
     *
     * @param  array $keys
     * @return array Array of found keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItems(array $keys)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Get metadata of an item.
     *
     * @param  string $key
     * @return array|bool Metadata on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadata($key)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Get multiple metadata
     *
     * @param  array $keys
     * @return array Associative array of keys and metadata
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadatas(array $keys)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItem($key, $value)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Store multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItems(array $keyValuePairs)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Add an item.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItem($key, $value)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Add multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItems(array $keyValuePairs)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Replace an existing item.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItem($key, $value)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Replace multiple existing items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItems(array $keyValuePairs)
    {
        $args = &func_get_args();
        $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed $token
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItem($key)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItems(array $keys)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItem($key)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @return array Array of not removed keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItems(array $keys)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Increment an item.
     *
     * @param  string $key
     * @param  int $value
     * @return int|bool The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItem($key, $value)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Increment multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItems(array $keyValuePairs)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Decrement an item.
     *
     * @param  string $key
     * @param  int $value
     * @return int|bool The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItem($key, $value)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Decrement multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItems(array $keyValuePairs)
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }

    /**
     * Capabilities of this storage
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
        $args = &func_get_args();
        return $this->handleMethodCall(__FUNCTION__, $args);
    }
}