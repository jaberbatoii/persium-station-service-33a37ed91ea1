<?php

namespace Persium\Station\Infrastructures\Persistency\Redis\Repositories;

use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Persium\Station\Domain\Services\Cache\StationDataRepositoryInterface;
use Persium\Station\Domain\ValueObjects\StationDataVO;

class StationDataRepository implements StationDataRepositoryInterface
{
    const LATEST_DATA_KEY = 'station_data:latest_data:';
    const CACHE_TTL_24_HOURS = 60 * 60 * 24; // 24 hours

    public function saveLatestData(StationDataVO $station_data_vo): void
    {
        $station = $station_data_vo->getStation();
        $station_id = $station->getId();
        Cache::put(self::LATEST_DATA_KEY . $station_id, $station_data_vo, self::CACHE_TTL_24_HOURS);
    }

    public function getLatestData(int $station_id): ?StationDataVO
    {
        return Cache::get(self::LATEST_DATA_KEY . $station_id);
    }

    public function getLatestTimestamp(int $station_id): ?DateTimeInterface
    {
        $latest_data_cache = $this->getLatestData($station_id);
        return $latest_data_cache?->getTimestamp();
    }
}
