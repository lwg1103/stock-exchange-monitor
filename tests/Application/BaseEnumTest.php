<?php

namespace Application;

class EnumStub extends BaseEnum
{
    const SOMETHING = 1;
    const SOMETHINGELSE = 'elephant';
}

class BaseEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function returnsValidKeys()
    {
        $actual = EnumStub::getValidKeys();
        $expected = 'SOMETHING, SOMETHINGELSE';

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function validatesKeys()
    {
        $this->assertTrue(EnumStub::isValid('1'));
        $this->assertTrue(EnumStub::isValid('elephant'));

        $this->assertFalse(EnumStub::isValid('otter'));
    }

    /**
     * @test
     */
    public function convertsKeysToString()
    {
        $expected = 'something';
        $actual = EnumStub::toString(EnumStub::SOMETHING);

        $this->assertEquals($expected, $actual);
    }
}