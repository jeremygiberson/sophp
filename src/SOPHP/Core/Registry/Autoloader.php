<?php


namespace SOPHP\Core\Registry;


use SOPHP\Core\Service\Contract;
use Zend\Code\Generator\ClassGenerator;
use Zend\Loader\StandardAutoloader;

class Autoloader extends StandardAutoloader implements  RegistryAwareInterface {
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $instance
     * @return self
     */
    public function setRegistry(Registry $instance)
    {
        $this->registry = $instance;
        return $this;
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Autoload a class
     *
     * @param   $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    public function autoload($class)
    {
        // attempt local filesystem loading
        $loaded = parent::autoload($class);

        return $loaded ?: $this->loadClassFromCloud($class);
    }


    /**
     * @param $class
     * @return mixed False or get_class($class)
     *
     */
    protected function loadClassFromCloud($class) {
        if(!$this->registry) {
            return false;
        }

        if($this->registry->isServiceRegistered($class)) {
            $contract = $this->registry->getServiceContract($class);
            return $this->generateProxy($contract) ? $class : false;
        }
        return false;
    }

    /**
     * @param Contract $contract
     * @return bool
     */
    protected function generateProxy(Contract $contract)
    {
        $generator = new ClassGenerator();
        $generator->setName($contract->getClassName());
        // todo: generate functional proxy class based on service mapping description of contract
        $source = $generator->generate();
        eval($source);
        return class_exists($contract->getClassName(), false);
    }
}
