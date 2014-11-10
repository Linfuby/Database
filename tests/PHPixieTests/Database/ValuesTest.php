<?php

namespace PHPixieTests\Database;

/**
 * @coversDefaultClass \PHPixie\Database\Values
 */
class ValuesTest extends \PHPixieTests\AbstractDatabaseTest
{
    protected $values;

    public function setUp()
    {
        $this->values = new \PHPixie\Database\Values;
    }
    
    /**
     * @covers ::orderBy
     * @covers ::<protected>
     */
    public function testOrderBy()
    {
        $orderBy = $this->values->orderBy('pixie', 'desc');
        $this->assertSame('pixie', $orderBy->field());
        $this->assertSame('desc', $orderBy->direction());
    }
}