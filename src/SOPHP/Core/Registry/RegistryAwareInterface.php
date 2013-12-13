<?php


namespace SOPHP\Core\Registry;


interface RegistryAwareInterface {
    /**
     * @param Registry $instance
     * @return mixed
     */
    public function setRegistry(Registry $instance);

    /**
     * @return Registry
     */
    public function getRegistry();
} 