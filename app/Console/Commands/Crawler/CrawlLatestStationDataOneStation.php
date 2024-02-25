<?php

declare(strict_types = 1);

namespace Persium\Station\Console\Commands\Crawler;

use Doctrine\ORM\EntityManager;
use Illuminate\Console\Command;
use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\StationRepository;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerFactory;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;
use Persium\Station\Infrastructures\Services\Crawler\StationDataCrawlerService;

class CrawlLatestStationDataOneStation extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "crawl:station:latest-data-one-station {--station-id=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl latest station data for one station";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(
        StationRepositoryInterface              $station_repository_interface,
        SensorTypeRepositoryInterface           $sensor_type_repository_interface,
        StationDataCacheInterface               $station_data_cache_interface,
        EntityManager                           $entity_manager,
    ) : int
    {
        $station_id = intval($this->option('station-id'));
        $station = $station_repository_interface->find($station_id);

        $crawler = $this->getCrawlerBySource(
            $station->getSource(),
            $station_repository_interface,
        );

        $station_data_crawler_service = new StationDataCrawlerService(
            $crawler,
            $station_repository_interface,
            $sensor_type_repository_interface,
            $station_data_cache_interface,
            $entity_manager,
        );

        $station_data_crawler_service->crawlLatestDataOneStation($station);
        return 0;
    }

    protected function getCrawlerBySource(
        string $source_name,
        StationRepository $station_repository,
    ): CrawlerInterface
    {
        $source = null;
        $source_configs = config('crawler');
        foreach ($source_configs as $root_source => $source_config) {
            if(in_array($source_name, $source_config['sources'])) {
                $source = $root_source;
                break;
            }
        }

        if (is_null($source)) {
            throw new \Exception('Source not found');
        }

        return CrawlerFactory::create(
            $source,
            $station_repository,
        );
    }
}

