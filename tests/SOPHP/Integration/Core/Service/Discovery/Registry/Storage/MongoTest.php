<?php


namespace SOPHP\Test\Integration\Core\Service\Discovery\Registry\Storage;

use MongoException;
use SOPHP\Core\Service\Discovery\Registry\Storage\Mongo;

class MongoTest extends \PHPUnit_Framework_TestCase {
    /** @var  Mongo */
    protected $mongo;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        if(!extension_loaded('mongo')){
            self::markTestSkipped('"mongo" extension must be loaded to run these tests.');
        }
    }


    public function setUp() {
        parent::setUp();

        $this->mongo = $this->getFreshMongo();
    }

    public function tearDown() {
        $this->mongo->getMongoCollection()->drop();
    }

    public function test() {
        $key = uniqid();
        $value = uniqid();
        $this->assertFalse($this->mongo->has($key));
        $this->mongo->add($key, $value);
        $this->assertTrue($this->mongo->has($key));
        $test = $this->mongo->get($key);
        $this->assertEquals($value, $test);
        $test = $this->mongo->getAll();
        $this->assertEquals(array($value), $test);

        $mongo = $this->getFreshMongo();
        $this->assertTrue($mongo->has($key));
        $test2 = $mongo->get($key);
        $this->assertEquals($value, $test2);

        $mongo->remove($key);
        $this->assertFalse($mongo->has($key));
        $this->assertNull($mongo->get($key));

        $this->assertFalse($this->mongo->has($key));
        $this->assertNull($this->mongo->get($key));

    }

    protected function getFreshMongo()
    {
        $mongo = new Mongo();
        $mongo->setHost(MONGO_HOST);
        $mongo->setPort(MONGO_PORT);
        $mongo->setUsername(MONGO_USER);
        $mongo->setPassword(MONGO_PASSWORD);
        $mongo->setDatabase(MONGO_DATABASE);

        try {
            $cname = 'collection'.uniqid();
            $db=$mongo->getClient()->{$mongo->getDatabase()};
            $collection = $db->{$cname};
            $collection->insert(array('test'=>$this->getName(),'datetime'=>date('Y-m-d H:i:s')));
            $db->dropCollection($cname);

        } catch (MongoException $e) {
            $this->markTestSkipped("Mongo connection not properly configured. See phpunit.xml to update connection values");
        }

        return $mongo;
    }
}
 