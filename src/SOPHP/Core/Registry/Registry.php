<?php


namespace SOPHP\Core\Registry;


use SOPHP\Core\Service\Contract;

class Registry {

    /**
     * @param string $class
     * @return bool
     */
    public function isServiceRegistered($class)
    {
        return false;
    }

    /**
     * @param $class
     * @return Contract
     */
    public function getServiceContract($class)
    {
        return new Contract($class);
    }


}