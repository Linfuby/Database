<?php
namespace PHPixieTests\Database\Driver\PDO\Adapter\Mysql\Parser;

/**
 * @coversDefaultClass \PHPixie\Database\Driver\PDO\Adapter\Mysql\Parser\Group
 */
class GroupTest extends \PHPixieTests\Database\SQL\Parser\GroupTest
{
    protected $expected = array(
        array('`a` = ?', array(1)),
        array('`a` = ? OR `b` = ? XOR NOT `c` = ?', array(1, 1, 1)),
        array('`a` = ? OR ( `b` = ? OR `c` = ? ) XOR NOT ( `d` = ? AND `e` = ? )', array(1, 1, 1, 1, 1)),
    );

    public function setUp()
    {
        parent::setUp();
        $fragmentParser = $this->database->driver('PDO')->fragmentParser('Mysql');
        $operatorParser = $this->database->driver('PDO')->operatorParser('Mysql', $fragmentParser);
        $this->groupParser = $this->database->driver('PDO')->groupParser('Mysql', $operatorParser);
    }

}