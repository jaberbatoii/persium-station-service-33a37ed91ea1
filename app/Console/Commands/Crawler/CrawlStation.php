<?php

declare(strict_types = 1);

namespace Persium\Station\Console\Commands\Crawler;

use Illuminate\Console\Command;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerFactory;
use Persium\Station\Infrastructures\Services\Crawler\StationCrawlerService;

class CrawlStation extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "crawl:station:info {--source=DEFRA}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl:station";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(
        StationRepositoryInterface $station_repository_interface,
    ): int {
        $source = $this->option('source');
        $crawler = CrawlerFactory::create(
            $source,
            $station_repository_interface,
        );
        $station_crawler_service = new StationCrawlerService(
            $crawler,
            $station_repository_interface
        );
        $station_crawler_service->crawl();
        return 0;
    }
}
