<?php

declare(strict_types = 1);

namespace Persium\Station\Console\Commands\Crawler;

use Illuminate\Console\Command;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Infrastructures\Services\Crawler\Factory\DEFRA\Crawler;
use Persium\Station\Jobs\Crawler\CrawlLatestDataOneStationJob;

class CrawlLatestStationDataAllStations extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "crawl:station:latest-data-all-station {--source=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl latest station data for one station";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(
        StationRepositoryInterface              $station_repository_interface,
    ): int
    {
        $source = $this->option('source');

        if ($source == 'DEFRA') {
            /** @var Crawler $crawler */
            $stations = $crawler->prepareStationsToGetLatestData();
        } else {
            $stations = $station_repository_interface->findBy([
                'source'    => $source
            ]);
        }

        $count = count($stations);
        $this->output->progressStart($count);
        foreach ($stations as $station) {
            dispatch(new CrawlLatestDataOneStationJob($station->getId()))->onQueue('crawler');
            $this->info("Dispatched job for station {$station->getName()}");
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        return 0;
    }
}

