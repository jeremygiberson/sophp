<?php
namespace SOPHP\Test;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testTrue() {
        $this->assertTrue(true);
    }
}
 