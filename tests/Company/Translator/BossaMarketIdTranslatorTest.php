<?php

namespace Company\Translator;

use Company\Entity\Company;
use Company\Entity\Company\Type;
use Prophecy\Prophet;

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
        $prophet = new Prophet();
        $this->companyRepository = $prophet->prophesize(EntityRepository::class);
        $pgn = new Company('PGNiG', 'PGN', Type::ORDINARY, 'PGNIG');
        $this->companyRepository->findOneBy(['marketId' => 'PGN'])->willReturn($pgn);
        $this->companyRepository->findOneBy(['longMarketId' => 'PGNIG'])->willReturn($pgn);
        
        $this->sut = new BossaMarketIdTranslator($this->companyRepository->reveal());
    }

}