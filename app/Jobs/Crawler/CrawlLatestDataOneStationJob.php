<?php

namespace Persium\Station\Jobs\Crawler;

use Doctrine\ORM\EntityManager;
use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerFactory;
use Persium\Station\Infrastructures\Services\Crawler\StationDataCrawlerService;
use Persium\Station\Jobs\Job;

class CrawlLatestDataOneStationJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly int $station_id,
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        StationRepositoryInterface              $station_repository_interface,
        SensorTypeRepositoryInterface           $sensor_type_repository_interface,
        StationDataCacheInterface               $station_data_cache_interface,
        EntityManager                           $entity_manager,
    ): void
    {
        $station = $station_repository_interface->find($this->station_id);
        if (!$station) {
            return;
        }

        $crawler_source = $station->getCrawlerSource();

        $crawler = CrawlerFactory::create(
            $crawler_source,
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
    }
}
