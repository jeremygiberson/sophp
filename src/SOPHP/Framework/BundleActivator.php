<?php


namespace SOPHP\Framework;


interface BundleActivator {
    /**
     * Called when this bundle is started so the Framework can perform the bundle specific activities necessary to start this bundle
     * @param BundleContext $context
     */
    public function start(BundleContext $context);

    /**
     * Called when this bundle is stopped so the Framework can perform bundle specific activities necessary to stop the bundle
     * @param BundleContext $context
     */
    public function stop(BundleContext $context);
} 