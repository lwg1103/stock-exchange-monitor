<?php

namespace Price\Downloader;

use Carbon\Carbon;

class BossaDownloaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var BossaDownloader */
    private $sut;

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

    protected function setUp()
    {
        $this->sut = new BossaDownloader();
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

        $date->subDay(abs($date->dayOfWeek - Carbon::WEDNESDAY));

        return $date;
    }

    private function getLastSunday()
    {
        $date = Carbon::now();

        $date->subDay(abs($date->dayOfWeek - Carbon::SUNDAY));

        return $date;
    }
}