<?php


namespace SOPHP\Core\Service\Discovery\Registry\Storage;
use MongoClient;

/**
 * Class Mongo
 * @package SOPHP\Core\Service\Discovery\Registry\Storage
 * TODO expand this very limited implementation of mongo driver
 *  -fully support mongoClient options w/ multiple hosts & authentication
 */
class Mongo implements AdapterInterface {
    /** @var  array */
    protected $options = array(
        'host' => 'localhost',
        'port' => '27017',
        'database' => 'so-php-registry',
        'collection' => 'so-php-services',
        'username' => null,
        'password' => null,
    );
    /** @var  MongoClient */
    protected $client;

    public function __construct($options = array()) {
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options) {
        if(isset($options['host'])){
            $this->setHost($options['host']);
        }
        if(isset($options['port'])){
            $this->setPort($options['port']);
        }
        if(isset($options['database'])) {
            $this->setDatabase($options['database']);
        }
        if(isset($options['collection'])){
            $this->setCollection($options['collection']);
        }
        if(isset($options['username'])){
            $this->setUsername($options['username']);
        }
        if(isset($options['password'])){
            $this->setPassword($options['password']);
        }
    }

    /**
     * @return array
     */
    public function getOptions(){
        return $this->options;
    }

    /**
     * @return MongoClient
     */
    public function getClient() {
        if(!$this->client) {
            $this->connect();
        }
        return $this->client;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function add($key, $value)
    {
        $doc = array('key' => $key, 'value' => $value);
        $this->getMongoCollection()->insert($doc);
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        $criteria = array('key' => $key);
        $this->getMongoCollection()->remove($criteria);
    }

    /**
     * Get entry for $key, or get all entries if $key is null
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $query = array('key' => $key);
        $result = $this->getMongoCollection()->findOne($query, array('value'));
        if($result) {
            return $result['value'];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $result = $this->getMongoCollection()->find(array(),array('value'));
        if($result) {
            return array_map(function($item){return $item['value'];}, $result);
        }
        return array();
    }

    /**
     * Check if $key exists in storage
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        if($this->get($key) !== null) {
            return true;
        }
        return false;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->options['host'] = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->options['host'];
    }

    /**
     * @param int $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->options['port'] = $port;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->options['port'];
    }

    /**
     * @param string $database
     * @return $this
     */
    public function setDatabase($database) {
        $this->options['database'] = $database;
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase() {
        return $this->options['database'];
    }

    /**
     * @param string $collection
     * @return $this
     */
    public function setCollection($collection) {
        $this->options['collection'] = $collection;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollection(){
        return $this->options['collection'];
    }

    /**
     * connects to Mongo
     */
    protected function connect()
    {
        $credentials = $this->buildCredentials();
        $connectionString = join('', array(
            'mongodb://',
            $credentials,
            $this->getHost(),
            ':'.$this->getPort(),
            '/'.$this->getDatabase()
        ));
        $this->client = new MongoClient($connectionString);
    }

    /**
     * @return \MongoCollection
     */
    public function getMongoCollection() {
        $database = $this->getDatabase();
        $collection = $this->getCollection();
        return $this->getClient()->{$database}->{$collection};
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->options['username'] = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(){
        return $this->options['username'];
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password) {
        $this->options['password'] = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(){
        return $this->options['password'];
    }

    /**
     * build credential string from options
     * @return string
     */
    protected function buildCredentials() {
        $credentials = '';
        if($this->getUsername()) {
            $credentials = $this->getUsername();
            if($this->getPassword()) {
                $credentials .= ':'.$this->getPassword();
            }
            $credentials.='@';
        }
        return $credentials;
    }
}