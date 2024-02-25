<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler;

use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\ValueObjects\StationVO;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;

class StationCrawlerService
{

    public function __construct(
        private readonly CrawlerInterface  $crawler,
        private readonly StationRepositoryInterface $station_repository_interface,
    ) {
    }

    public function crawl(): void
    {
        $station_vos = $this->crawler->getStations();
        /* @var StationVO $station_vo */
        foreach ($station_vos as $station_vo) {
            if (!($station_vo instanceof StationVO)){
                throw new \Exception('StationVO expected');
            }

            $station = $this->station_repository_interface->findOneBy([
                'source' => $station_vo->getSource(),
                'source_id' => $station_vo->getSourceID(),
            ]);
            if (!$station) {
                $station = Station::createFromVO($station_vo);
                $this->station_repository_interface->save($station);
            }
        }
    }
}
