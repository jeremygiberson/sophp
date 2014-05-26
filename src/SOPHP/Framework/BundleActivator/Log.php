<?php


namespace SOPHP\Framework\BundleActivator;


use SOPHP\Framework\BundleActivator;
use SOPHP\Framework\BundleContext;
use Zend\Log\Logger as Logger;
class Log implements BundleActivator {

    /**
     * Called when this bundle is started so the Framework can perform the bundle specific activities necessary to start this bundle
     * @param BundleContext $context
     */
    public function start(BundleContext $context)
    {
        /** @var Logger $log */
        $log = $context->getService('Zend\Log\Logger');
        if($log){
            $log->info($context->getBundle()->getSymbolicName() . " started");
        }
    }

    /**
     * Called when this bundle is stopped so the Framework can perform bundle specific activities necessary to stop the bundle
     * @param BundleContext $context
     */
    public function stop(BundleContext $context)
    {
        /** @var Logger $log */
        $log = $context->getService('Zend\Log\Logger');
        if($log){
            $log->info($context->getBundle()->getSymbolicName() . " stopped");
        }
    }
}