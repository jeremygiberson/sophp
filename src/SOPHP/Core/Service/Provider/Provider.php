<?php


namespace SOPHP\Core\Service\Provider;


use SOPHP\Core\Service\Service;
use SplObjectStorage;

class Provider {
    /** @var  SplObjectStorage */
    protected $strategyPreferences;

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

        switch($strategy) {
            case Strategy::Local:
                return $this->getLocalInstance($service);
                break;
            case Strategy::Proxy:
                return $this->getProxyInstance($service);
                break;
        }
    }


    protected function getLocalInstance($service)
    {
        // todo
    }

    protected function getProxyInstance($service)
    {
        // todo
    }
} 