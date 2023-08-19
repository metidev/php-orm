<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
//    public function testTwoPlusTwoResultsInFour()
//    {
//        $this->assertEquals(4, 2 + 2);
//    }

    /**
     * @test
     */
    public function twoPlusTwoResultsInFour()
    {
        $this->assertEquals(4, 2 + 2);
    }
}