<?php


namespace SOPHP\Core\Service\Discovery\Registry\Storage;


class Memory implements AdapterInterface {
    protected $storage = array();
    /**
     * @param string $key
     * @param string $value
     */
    public function add($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        unset($this->storage[$key]);
    }

    /**
     * Get entry for $key, or get all entries if $key is null
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->storage[$key];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->storage;
    }

    /**
     * Check if $key exists in storage
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->storage[$key]);
    }
}