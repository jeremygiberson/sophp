<?php
namespace SOPHP\Test\TestCase;


use PHPUnit_Framework_TestCase;

/**
 * Class WebServer
 * @package SOPHP\Test\TestCase
 * @thanks http://tech.vg.no/2013/07/19/using-phps-built-in-web-server-in-your-test-suites/
 */
abstract class WebServer extends PHPUnit_Framework_TestCase {
    protected static $__pIds = array();
    protected static $__startCommand = 'php -S %s:%d %s >/dev/null 2>&1 & echo $!';
    protected static $__infoStart = '%s - Web server started on %s:%d with PID %d';
    protected static $__infoKill = '%s - Killing process with ID %d';
    protected $__pid;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        $version = explode('.', phpversion());
        if($version[0] < 5 || $version[1] < 4) {
            self::markTestSkipped("PHP version >=5.4.0 is required to run this test");
            return;
        }

        register_shutdown_function('SOPHP\Test\TestCase\WebServer::killProcesses');
    }

    public static function tearDownAfterClass() {
        self::killProcesses();
        parent::tearDownAfterClass();
    }

    /**
     * kills processes started by test case
     */
    public static function killProcesses() {
        foreach(self::$__pIds as $pid => $active) {
            if(self::$__infoKill) {
                echo sprintf(self::$__infoKill, date('r'), $pid) . PHP_EOL;
            }
            exec('kill ' . $pid);
            unset(self::$__pIds[$pid]);
        };
    }

    /**
     * starts web server
     */
    public function setUp() {
        parent::setUp();

        if(!file_exists($this->getRouterFile())) {
            throw new \RuntimeException(get_called_class() . ' getRouterFile does not return a valid file path');
        }

        $command = sprintf(
            self::$__startCommand,
            self::getWebServerHost(),
            self::getWebServerPort(),
            $this->getRouterFile()
        );

        $output = array();
        exec($command, $output);
        $this->__pid = (int) $output[0];
        self::$__pIds[$this->__pid] = true;

        if(self::$__infoStart) {
            echo sprintf(
                    self::$__infoStart,
                    date('r'),
                    self::getWebServerHost(),
                    self::getWebServerPort(),
                    $this->__pid
                ) . PHP_EOL;
        }
    }

    /**
     * shuts down web server
     */
    public function tearDown() {
        if(self::$__infoKill) {
            echo sprintf(self::$__infoKill, date('r'), $this->__pid) . PHP_EOL;
        }
        exec('kill ' . $this->__pid);
        unset(self::$__pIds[$this->__pid]);

        parent::tearDown();
    }

    /**
     * @return string hostname
     */
    protected static function getWebServerHost(){
        return WEB_SERVER_HOST;
    }

    /**
     * @return int port
     */
    protected static function getWebServerPort() {
        return WEB_SERVER_PORT;
    }

    /**
     * @return string
     */
    protected static function getWebServerUri() {
        return 'http://' . WEB_SERVER_HOST . ':' . WEB_SERVER_PORT;
    }

    /** @return string absolute path to router file */
    abstract protected function getRouterFile();
} 
