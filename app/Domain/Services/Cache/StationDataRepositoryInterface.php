<?php

namespace Persium\Station\Domain\Services\Cache;

use DateTimeInterface;
use Persium\Station\Domain\ValueObjects\StationDataVO;

interface StationDataRepositoryInterface
{
    public function saveLatestData(StationDataVO $station_data_vo): void;

    public function getLatestData(int $station_id): ?StationDataVO;

    public function getLatestTimestamp(int $station_id): ?DateTimeInterface;

}
