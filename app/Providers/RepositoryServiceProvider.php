<?php

declare(strict_types = 1);

namespace Persium\Station\Providers;

use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\SensorType\VendorRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorRepositoryInterface;
use Persium\Station\Domain\Entities\Wind\WindDataRepositoryInterface;
use Persium\Station\Domain\Entities\Wind\WindPointRepositoryInterface;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\SensorTypeRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\StationAQIDataRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\StationRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\StationSensorAQIDataRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\StationSensorDataRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\StationSensorRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\VendorRepository;
use Illuminate\Support\ServiceProvider;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\WindDataRepository;
use Persium\Station\Infrastructures\Persistency\Doctrine\Repositories\WindPointRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            StationRepositoryInterface::class,
            StationRepository::class
        );

        $this->app->bind(
            StationSensorRepositoryInterface::class,
            StationSensorRepository::class
        );

        $this->app->bind(
            SensorTypeRepositoryInterface::class,
            SensorTypeRepository::class
        );

        $this->app->bind(
            VendorRepositoryInterface::class,
            VendorRepository::class
        );

        $this->app->bind(
            StationSensorDataRepositoryInterface::class,
            StationSensorDataRepository::class
        );

        $this->app->bind(
            StationAQIDataRepositoryInterface::class,
            StationAQIDataRepository::class
        );

        $this->app->bind(
            StationSensorAQIDataRepositoryInterface::class,
            StationSensorAQIDataRepository::class
        );

        $this->app->bind(
            WindPointRepositoryInterface::class,
            WindPointRepository::class
        );

        $this->app->bind(
            WindDataRepositoryInterface::class,
            WindDataRepository::class
        );
    }
}
