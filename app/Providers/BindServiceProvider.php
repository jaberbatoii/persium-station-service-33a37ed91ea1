<?php

namespace Persium\Station\Providers;

use Illuminate\Support\ServiceProvider;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Domain\Services\Cache\StationDataRepositoryInterface;
use Persium\Station\Domain\Services\Helper\MapHelper;
use Persium\Station\Infrastructures\Persistency\Redis\Repositories\StationDataRepository;
use Persium\Station\Infrastructures\Services\StationDataCache;

class BindServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind(
            StationDataRepositoryInterface::class,
            StationDataRepository::class
        );

        $this->app->bind(
            StationDataCacheInterface::class,
            StationDataCache::class
        );

        // singleton for MapHelper
        $this->app->singleton(MapHelper::class);
    }
}
