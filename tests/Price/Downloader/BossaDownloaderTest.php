<?php

namespace Price\Downloader;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use Prophecy\Prophet;
use Prophecy\Prophecy\ObjectProphecy;

class BossaDownloaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var BossaDownloader */
    private $sut;
    /** @var Client|ObjectProphecy */
    private $httpClient;

    /**
     * @test
     */
    public function returnsRawData()
    {
        $this->assertInstanceOf(
            RawData::class,
            $this->sut->download($this->getLastWednesday())
        );
    }

    /**
     * @test
     *
     * @dataProvider rowsDataProvider
     */
    public function downloadsExpectedFile($row)
    {
        $this->assertCount(
            1,
            preg_grep($row, $this->sut->download($this->getLastWednesday())->rows)
        );
    }

    /**
     * @test
     *
     * @expectedException Price\Downloader\NoFileException
     */
    public function throwsExceptionIfThereIsNoReportForGivenDay()
    {
        $this->sut->download($this->getLastSunday());
    }

    /**
     * @test
     */
    public function downloadsFileOnlyOnceForGivenDay()
    {
        $this->httpClient->get(Argument::any())->shouldBeCalledTimes(1)->willReturn(new Response());

        $this->sut->setClient($this->httpClient->reveal());

        $this->sut->download($this->getLastThursday());
        $this->sut->download($this->getLastThursday());
        $this->sut->download($this->getLastThursday());
        
        $this->httpClient->checkProphecyMethodsPredictions();
    }
    
    protected function setUp()
    {
        $prophet = new Prophet();

        $this->sut = new BossaDownloader();
        $this->httpClient = $prophet->prophesize(Client::class);
    }

    public function rowsDataProvider()
    {
        return [
            ["/PKOBP," . $this->getLastWednesday()->format("Ymd") ."*/"],
            ["/BOGDANKA," . $this->getLastWednesday()->format("Ymd") . "*/"],
            ["/ASSECOPOL," . $this->getLastWednesday()->format("Ymd") . "*/"],
            ["/PGNIG," . $this->getLastWednesday()->format("Ymd") . "*/"]
        ];
    }

    private function getLastWednesday()
    {
        $date = Carbon::now();

        if ($date->dayOfWeek < Carbon::WEDNESDAY)
            $date->subDay(Carbon::WEDNESDAY);

        $date->subDay(abs($date->dayOfWeek - Carbon::WEDNESDAY));

        return $date;
    }

    private function getLastThursday()
    {
        $date = Carbon::now();

        if ($date->dayOfWeek < Carbon::THURSDAY)
            $date->subDay(Carbon::THURSDAY);

        $date->subDay(abs($date->dayOfWeek - Carbon::THURSDAY));

        return $date;
    }

    private function getLastSunday()
    {
        $date = Carbon::now();

        $date->subDay(abs($date->dayOfWeek - Carbon::SUNDAY));

        return $date;
    }
}