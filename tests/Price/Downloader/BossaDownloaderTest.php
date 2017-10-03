<?php

namespace Price\Downloader;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;
use Prophecy\Prophecy\ObjectProphecy;

class BossaDownloaderTest extends TestCase
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
     * @dataProvider rowsDataProviderForCurrentMonth
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
     * @dataProvider rowsDataProviderForPreviousMonth
     */
    public function downloadsFileFromPreviousMonth($row)
    {
        $this->assertCount(
            1,
            preg_grep($row, $this->sut->download($this->getWednesdayMonthAgo())->rows)
        );
    }


    /**
     * @test
     *
     * @dataProvider rowsDataProviderForPreviousYear
     */
    public function downloadsFileFromPreviousYear($row)
    {
        $this->assertCount(
            1,
            preg_grep($row, $this->sut->download($this->getWednesdayYearAgo())->rows)
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

    public function rowsDataProviderForCurrentMonth()
    {
        return [
            ["/PKOBP," . $this->getLastWednesday()->format("Ymd") ."*/"],
            ["/BOGDANKA," . $this->getLastWednesday()->format("Ymd") . "*/"],
            ["/ASSECOPOL," . $this->getLastWednesday()->format("Ymd") . "*/"],
            ["/PGNIG," . $this->getLastWednesday()->format("Ymd") . "*/"]
        ];
    }

    public function rowsDataProviderForPreviousMonth()
    {
        return [
            ["/PKOBP," . $this->getWednesdayMonthAgo()->format("Ymd") ."*/"],
            ["/BOGDANKA," . $this->getWednesdayMonthAgo()->format("Ymd") . "*/"],
            ["/ASSECOPOL," . $this->getWednesdayMonthAgo()->format("Ymd") . "*/"],
            ["/PGNIG," . $this->getWednesdayMonthAgo()->format("Ymd") . "*/"]
        ];
    }

    public function rowsDataProviderForPreviousYear()
    {
        return [
            ["/PKOBP," . $this->getWednesdayYearAgo()->format("Ymd") ."*/"],
            ["/BOGDANKA," . $this->getWednesdayYearAgo()->format("Ymd") . "*/"],
            ["/ASSECOPOL," . $this->getWednesdayYearAgo()->format("Ymd") . "*/"],
            ["/PGNIG," . $this->getWednesdayYearAgo()->format("Ymd") . "*/"]
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

    private function getWednesdayMonthAgo()
    {
        /** @var Carbon $lastWednesday */
        $lastWednesday = $this->getLastWednesday();

        return $lastWednesday->subDay(28);
    }

    private function getWednesdayYearAgo()
    {
        /** @var Carbon $lastWednesday */
        $lastWednesday = $this->getLastWednesday();

        return $lastWednesday->subDay(7*52);
    }

    protected function setUp()
    {
        $prophet = new Prophet();

        $this->sut = new BossaDownloader();
        $this->httpClient = $prophet->prophesize(Client::class);
    }
}