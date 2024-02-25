<?php

namespace Persium\Station\Domain\Services\Cache;

use DateTimeInterface;
use Persium\Station\Domain\ValueObjects\StationAQIDataVO;
use Persium\Station\Domain\ValueObjects\StationDataVO;

interface StationDataCacheInterface
{
    public function getLatestTimestamp(int $station_id): ?DateTimeInterface;

    public function saveLatestData(StationDataVO $station_data_vo): void;

    public function getLatestData(int $station_id): ?StationDataVO;

    public function getLatestAQIData(int $station_id, int $aqi_type): ?StationAQIDataVO;

}
