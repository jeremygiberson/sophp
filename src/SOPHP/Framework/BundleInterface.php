<?php


namespace SOPHP\Framework;


interface BundleInterface {
    /** the bundle has been uninstalled, it cannot move to another state */
    const STATE_UNINSTALLED = 0;
    /** The bundle has been successfully installed */
    const STATE_INSTALLED = 1;
    /** All classes the bundle needs are available. This state indicates either
     * the bundle is ready to be started or has stopped */
    const STATE_RESOLVED = 2;
    /** The bundle is being started, BundleActivator.start method will be called
     * and has not yet returned.  */
    const STATE_STARTING = 4;
    /** The bundle is being stopped. The BundleActivator.stop method has been
     * called but the stop method has not yet returned. */
    const STATE_STOPPING = 8;
    /** The bundle has been succcessfully activated and is running; the
     * BundleActivator.start method has been called and returned. */
    const STATE_ACTIVE = 16;


    /** @return string returns the bundles unique identifier */
    public function getBundleId();
    /** @return int returns the bundle's current state */
    public function getState();
    /** @return Version */
    public function getVersion();
    /** @return string */
    public function getSymbolicName();
    /** @return ServiceReference[] */
    public function getRegisteredServices();

} 