<?php

declare(strict_types = 1);

namespace Persium\Station\Console\Commands\Crawler;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerFactory;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;
use Persium\Station\Infrastructures\Services\Crawler\StationDataCrawlerService;
use Persium\Station\Jobs\Crawler\CrawlHistoricDataOneStationJob;

class CrawlHistoricDataAllStations extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "crawl:station:historic-data:all-stations {--source=} {--start-time=} {--end-time=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl historic station data";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(
        StationRepositoryInterface              $station_repository,
    ): int {
        $source = $this->option('source');
        $start_time = Carbon::createFromTimeString($this->option('start-time'));
        $end_time = Carbon::createFromTimeString($this->option('end-time'));
        $crawler_config = config('crawler');
        $stations = $station_repository->findBy(['source' => $crawler_config[$source]['sources']]);
        foreach ($stations as $station) {
            dispatch(new CrawlHistoricDataOneStationJob($station->getID(), $start_time, $end_time))->onQueue('crawler');
            $this->info('dispatched station ' . $station->getID());
        }

        $this->info('Done');
        return 0;
    }


}
