<?php
namespace PHPixieTests\Database\Driver\PDO;

class PDOConnectionTestStub extends \PHPixie\Database\Driver\PDO\Connection
{
    protected function connect($connection, $user, $password, $options)
    {
        if (substr($connection, 0, 7) != 'sqlite:' || $user != 'test' || $password != 5 || $options !== array('someOption' => 5))
            throw new \Exception("Parameters don't match expected");

        return parent::connect($connection, $user, $password, array());
    }

    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }
}

/**
 * @coversDefaultClass \PHPixie\Database\Driver\PDO\Connection
 */
class ConnectionTest extends \PHPixieTests\Database\ConnectionTest
{
    protected $databaseFile;
    protected $config;
    protected $queryClass = 'PHPixie\Database\Driver\PDO\Query';
    protected $pdoProperty;

    public function setUp()
    {
        $this->databaseFile = tempnam(sys_get_temp_dir(), 'test.sqlite');
        $this->config = $this->sliceStub(array(
            'connection' => 'sqlite:'.$this->databaseFile,
            'user'       => 'test',
            'password'   =>  5,
            'driver'     => 'PDO',
            'connectionOptions' => array(
                'someOption' => 5
            ))
        );
        $this->database = $this->getMock('\PHPixie\Database', array('get'), array(null));
        $reflection = new \ReflectionClass("\PHPixie\Database\Driver\PDO\Connection");
        $this->pdoProperty = $reflection->getProperty('pdo');
        $this->pdoProperty->setAccessible(true);

        $this->connection = new PDOConnectionTestStub($this->database->driver('PDO'), 'test', $this->config);
        $this->connection->execute("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255))");
        $this->database
                ->expects($this->any())
                ->method('get')
                ->with ('test')
                ->will($this->returnValue($this->connection));
    }

    public function tearDown()
    {
        $this->pdoProperty->setValue($this->connection, null);
        unlink($this->databaseFile);
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $this->connection->execute("INSERT INTO fairies(id,name) VALUES (1,'Tinkerbell')");
        $result = $this->connection->execute("Select * from fairies where id = ?", array(1));
        $this->assertEquals(array((object) array('id'=>1, 'name'=>'Tinkerbell')),$result->asArray());
    }
    
    /**
     * @covers ::insertId
     */
    public function testInsertId()
    {
        $this->connection->execute("INSERT INTO fairies(id,name) VALUES (1,'Tinkerbell')");
        $this->assertEquals(1, $this->connection->insertId());
    }
    
    /**
     * @covers ::listColumns
     */
    public function testListColumns()
    {
        $this->assertEquals(array('id', 'name'), $this->connection->listColumns('fairies'));
    }

    /**
     * @covers ::pdo
     */
    public function testPdo()
    {
        $this->assertInstanceOf('\PDO', $this->connection->pdo());
    }

    /**
     * @covers ::adapterName
     */
    public function testAdapterName()
    {
        $this->assertEquals('Sqlite', $this->connection->adapterName());
    }

    /**
     * @covers ::execute
     */
    public function testException()
    {
        $this->setExpectedException('\Exception');
        $this->connection->execute('pixie');
    }

    public function testWrongOptionsException()
    {
        $this->setExpectedException('\PHPixie\Database\Exception');
        $config = $this->sliceStub(array(
            'connection' => 'sqlite:'.$this->databaseFile,
            'user'   => 'pixie',
            'password' => 5,
            'connectionOptions' => 5
        ));
        $connection = new \PHPixie\Database\Driver\Mongo\Connection($this->database->driver('PDO'), 'test', $config);
    }

}