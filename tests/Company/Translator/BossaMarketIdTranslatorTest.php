<?php

namespace Company\Translator;

class BossaMarketIdTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var BossaMarketIdTranslator */
    private $sut;

    /**
     * @test
     */
    public function translatesBossaIdToMarketId()
    {
        $this->assertEquals(
            'PGN',
            $this->sut->translateToMarketId('PGNIG')
        );
    }

    /**
     * @test
     *
     * @expectedException Company\Translator\TranslatorException
     */
    public function throwsExceptionIfBossaIdIsMissing()
    {
        $this->sut->translateToMarketId('missing_id');
    }

    /**
     * @test
     */
    public function translatesMarketIdToBossaId()
    {
        $this->assertEquals(
            'PGNIG',
            $this->sut->translateFromMarketId('PGN')
        );
    }

    /**
     * @test
     *
     * @expectedException Company\Translator\TranslatorException
     */
    public function throwsExceptionIfMarketIdIsMissing()
    {
        $this->sut->translateFromMarketId('missing_id');
    }

    protected function setUp()
    {
        $this->sut = new BossaMarketIdTranslator();
    }

}