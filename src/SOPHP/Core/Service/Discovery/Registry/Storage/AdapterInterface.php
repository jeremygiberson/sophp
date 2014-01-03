<?php


namespace SOPHP\Core\Service\Discovery\Registry\Storage;


interface AdapterInterface {
    /**
     * @param string $key
     * @param string $value
     */
    public function add($key, $value);

    /**
     * @param string $key
     */
    public function remove($key);

    /**
     * Get entry for $key, or get all entries if $key is null
     * @param string $key
     * @return string
     */
    public function get($key);

    /**
     * @return array
     */
    public function getAll();

    /**
     * Check if $key exists in storage
     * @param string $key
     * @return bool
     */
    public function has($key);
} 