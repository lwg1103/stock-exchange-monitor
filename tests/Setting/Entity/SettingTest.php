<?php

namespace Setting\Entity;

use Setting\Entity\Setting;

class SettingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Setting
     */
    private $sut;

    private $name;
    private $value;
    private $section;

    /**
     * @test
     */
    public function storesValue()
    {
        $this->assertEquals($this->value, $this->sut->getValue());
    }

    /**
     * @test
     */
    public function storesSection()
    {
        $this->assertEquals($this->section, $this->sut->getSection());
    }

    /**
     * @test
     */
    public function storesName()
    {
        $this->assertEquals($this->name, $this->sut->getName());
    }

    protected function setUp()
    {
        $this->name = 'mh_setting';
        $this->value = 'mh_value';
        $this->section = 'mh_section';
        $this->sut = $this->createSetting();
    }

    private function createSetting() {
        return Setting::create($this->name, $this->value, $this->section);
    }

}