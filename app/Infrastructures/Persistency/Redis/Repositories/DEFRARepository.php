<?php

namespace Persium\Station\Infrastructures\Persistency\Redis\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DEFRARepository
{
    const CACHE_REMEMBER_24_HOURS = 24 * 60 * 60;
    const GET_LATEST_DATA_CONFIG_PREFIX = 'crawl_latest_config_';

    public function __construct(
    ) {
    }

    public function setLatestDataConfig(int $station_id, array $config): void
    {
        Cache::put(self::GET_LATEST_DATA_CONFIG_PREFIX . $station_id, $config, self::CACHE_REMEMBER_24_HOURS);
    }

    public function getLatestDataConfig(int $station_id): array
    {
        return Cache::get(self::GET_LATEST_DATA_CONFIG_PREFIX . $station_id, []);
    }
}
