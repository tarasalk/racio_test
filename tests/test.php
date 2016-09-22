<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Plp\Task\App;
use medoo;

class AppTest extends TestCase {
    private $config = [
        'database_type' => 'mysql',
        'database_name' => 'racio_test',
        'server' => 'localhost',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ];

    public function setUp() {
        $db = $this->getConnection();

        $db->query('DROP TABLE task');
    }

    protected function initApp() {
        return new App($this->config);
    }

    protected function getConnection() {
        return new medoo($this->config);
    }

    public function testConstructTypeError() {
        $error = false;

        try {
            new App(1);
        }
        catch (\TypeError $e) {
            $error = true;
        }

        $this->assertTrue($error);
    }

    public function testDbConnection() {
        $app = $this->initApp();

        $this->assertInstanceOf(medoo::class, $app->getConnection());
    }

    public function testGetTask() {
        $app = $this->initApp();

        $aTask = $this->invokeMethod($app, 'getTask');

        $this->assertEmpty($aTask);
    }

    public function testMigrate() {
        $app = $this->initApp();

        $this->invokeMethod($app, 'migrate');

        $db = $this->getConnection();

        $this->assertEquals(6, $db->count('task'));
    }

    public function testTaskDeffered() {
        $app = $this->initApp();

        $this->invokeMethod($app, 'migrate');
        $aTask = $this->invokeMethod($app, 'getTask');
    
        $result = ['result' => 123];
        $this->invokeMethod($app, 'taskDeffered', [$aTask, $result]);

        $db = $this->getConnection();
        $aCheckTask = $db->get('task', '*', ['id' => $aTask['id']]);
        
        $this->assertEquals(1, $aCheckTask['retries']);
        $this->assertEquals(0, $aCheckTask['status']);
        $this->assertEquals(json_encode($result), $aCheckTask['result']);
        $this->assertNotNull($aCheckTask['deffer']);
    }

    public function testTaskFailed() {
        $app = $this->initApp();

        $this->invokeMethod($app, 'migrate');
        $aTask = $this->invokeMethod($app, 'getTask');

        $result = ['result' => 321];
        $this->invokeMethod($app, 'taskFailed', [$aTask, $result]);

        $db = $this->getConnection();
        $aCheckTask = $db->get('task', '*', ['id' => $aTask['id']]);

        $this->assertEquals(0, $aCheckTask['retries']);
        $this->assertEquals(-1, $aCheckTask['status']);
        $this->assertEquals(json_encode($result), $aCheckTask['result']);
        $this->assertNull($aCheckTask['deffer']);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}