<?php

namespace Persium\Station\Jobs\Crawler;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerFactory;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;
use Persium\Station\Infrastructures\Services\Crawler\StationDataCrawlerService;
use Persium\Station\Jobs\Job;

class CrawlHistoricDataOneStationJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly int $station_id,
        private readonly Carbon $start_time,
        private readonly Carbon $end_time,
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
        StationRepositoryInterface              $station_repository,
        SensorTypeRepositoryInterface           $sensor_type_repository,
        StationDataCacheInterface               $station_data_cache,
        EntityManager                           $entity_manager,
    ): void
    {
        $station_id = $this->station_id;
        $start_time = $this->start_time;
        $end_time = $this->end_time;

        $station = $station_repository->find($station_id);

        $crawler = $this->getCrawlerBySource(
            $station->getSource(),
            $station_repository,
        );

        $station_data_crawler_service = new StationDataCrawlerService(
            $crawler,
            $station_repository,
            $sensor_type_repository,
            $station_data_cache,
            $entity_manager,
        );

        $station_data_crawler_service->crawlDataOneStationInRange($station, $start_time, $end_time);
    }

    protected function getCrawlerBySource(
        string                           $source_name,
        StationRepositoryInterface       $station_repository,
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
