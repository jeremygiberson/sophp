<?php


namespace SOPHP\Zend\Cache\Storage;


use Zend\Cache\Storage\StorageInterface;

interface StorageAwareInterface {
    /**
     * @param StorageInterface $instance
     * @return mixed
     */
    public function setStorageAdapter(StorageInterface $instance);

    /**
     * @return StorageInterface
     */
    public function getStorageAdapter();
} 