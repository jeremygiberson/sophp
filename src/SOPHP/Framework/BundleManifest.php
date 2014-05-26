<?php


namespace SOPHP\Framework;


class BundleManifest {
    /** @var  string Optional: Human readable name */
    protected $name;
    /** @var  string Required: unique identifier for bundle (prefer reverse domain name convention) */
    protected $symbolicName;
    /** @var  string Optional: Human readable description of bundle */
    protected $description;
    /** @var  Version Optional: Specifies Framework specification used to read bundle */
    protected $manifestVersion;
    /** @var  Version Optional: Specifies the version number to the bundle */
    protected $version;
    /** @var  string Optional: Fully Qualified Class Name of BundleActivator implementation for bundle */
    protected $activator;
    /** @var  string[] Optional: Expresses which php interfaces contained in bundle will be made available to outside world */
    protected $exportPackage;
    /** @var  string[] Optional: Indicates which php interfaces will be required from the outside world to fulfill dependencies */
    protected $importPackage;
} 