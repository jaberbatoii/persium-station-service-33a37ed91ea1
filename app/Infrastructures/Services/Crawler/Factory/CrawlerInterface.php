<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler\Factory;

use Carbon\Carbon;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\ValueObjects\StationDataVO;

interface CrawlerInterface
{
    public function getStations() : array;
    public function getDataInTimeRangeOneStation(Station $station, Carbon $start_time, Carbon $end_time): array;
    public function getLatestDataOneStation(Station $station): array;
    public function transformOneDataToVO(Station $station, array $data): StationDataVO;
    public function canSaveLatestDataToDatabase(): bool;
}
