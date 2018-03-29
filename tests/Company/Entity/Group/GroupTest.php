<?php

namespace Company\Entity\Company;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Group
     */
    private $sut;

    /**
     * @test
     */
    public function setsType()
    {
        $this->sut->setType(Group\Type::SECTOR);

        $this->assertEquals(Group\Type::SECTOR, $this->sut->getType());
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function throwsExceptionIfTypeIsInvalid()
    {
        $this->sut->setType('trolollo');
    }

    /**
     * @test
     */
    public function canBeConvertedToString()
    {
        $expected = "WIG20";

        $this->assertEquals($expected, (string) $this->sut);
    }

    protected function setUp()
    {
        $this->sut = new Group("WIG20", Group\Type::INDEX);
    }
}