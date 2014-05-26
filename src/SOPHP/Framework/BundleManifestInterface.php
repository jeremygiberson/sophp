<?php


namespace SOPHP\Framework;


interface BundleManifestInterface {
    /**
     * @return Version
     */
    public function getVersion();

    /**
     * @return Version
     */
    public function getManifestVersion();

    /**
     * @return string
     */
    public function getSymbolicName();

    /**
     * @return mixed
     */
    public function getActivationPolicy();

    public function getImportPackage();
    public function getExportPackage();
} 