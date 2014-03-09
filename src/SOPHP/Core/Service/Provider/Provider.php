<?php


namespace SOPHP\Core\Service\Provider;


use SOPHP\Core\Proxy\Builder\ServiceBuilder;
use SOPHP\Core\Proxy\Proxy;
use SOPHP\Core\Service\Provider\Exception\MissingServicePreference;
use SOPHP\Core\Service\Service;
use SplObjectStorage;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Provider
 * Provides concrete instance of service based on strategy preference (configurable).
 * Strategy can force proxy usage over local usage even when local is available.
 * @package SOPHP\Core\Service\Provider
 */
class Provider implements ServiceLocatorAwareInterface {
    /** @var  SplObjectStorage */
    protected $strategyPreferences;
    /** @var  ServiceLocatorInterface */
    protected $serviceLocator;

    /**
     * @param \SplObjectStorage $strategyPreferences
     */
    public function setStrategyPreferences(SplObjectStorage $strategyPreferences)
    {
        $this->strategyPreferences = $strategyPreferences;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getStrategyPreferences()
    {
        if(!$this->strategyPreferences) {
            $this->strategyPreferences = new SplObjectStorage();
        }
        return $this->strategyPreferences;
    }

    /**
     * @param Service $service
     * @param Strategy $strategy
     */
    public function setStrategyPreference(Service $service, Strategy $strategy) {
        $preferences = $this->getStrategyPreferences();
        $preferences->offsetSet($service, $strategy);

    }

    /**
     * @param Service $service
     * @return Strategy
     */
    public function getStrategyPreference(Service $service) {
        $preferences = $this->getStrategyPreferences();
        if($preferences->contains($service)) {
            return $preferences->offsetGet($service);
        }
        return new Strategy(Strategy::Proxy); // use default strategy
    }

    /**
     * @param Service $service
     * @return mixed
     */
    public function getInstance(Service $service) {
        $strategy = $this->getStrategyPreferences()->contains($service) ?
            $this->getStrategyPreferences()->offsetGet($service) : new Strategy();

        switch($strategy->getValue()) {
            case Strategy::Local:
                return $this->getLocalInstance($service);
                break;
            case Strategy::Proxy:
                return $this->getProxyInstance($service);
                break;
        }
    }

    /**
     * @param Service $service
     * @return mixed
     * @throws Exception\MissingServicePreference
     */
    protected function getLocalInstance(Service $service)
    {
        if(!$this->getServiceLocator()->has($service->getInterface())) {
            throw new MissingServicePreference($service);
        }
        return $this->getServiceLocator()->get($service->getInterface());
    }

    /**
     * @param Service $service
     * @return Proxy
     */
    protected function getProxyInstance(Service $service)
    {
        $builder = new ServiceBuilder($service);
        $class = $builder->build(uniqid('proxy'));
        return new $class();
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        if(!$this->serviceLocator) {
            $this->serviceLocator = new ServiceManager();
        }
        return $this->serviceLocator;
    }
}