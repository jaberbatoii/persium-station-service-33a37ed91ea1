<?php

declare(strict_types = 1);

namespace Persium\Station\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Persium\Station\Console\Commands\Crawler\CrawlLatestStationDataAllStations;
use Persium\Station\Console\Commands\Crawler\CrawlLatestStationDataOneStation;
use Persium\Station\Console\Commands\Crawler\CrawlStation;
use Persium\Station\Console\Commands\Crawler\CrawlHistoricOneStationData;
use Persium\Station\Console\Commands\Test\TestExample;
use Persium\Station\Console\Commands\WindMap\CrawlWindMapData;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CrawlHistoricOneStationData::class,
        CrawlStation::class,
        CrawlLatestStationDataOneStation::class,
        CrawlLatestStationDataAllStations::class,
        CrawlWindMapData::class,
        TestExample::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
