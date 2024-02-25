<?php

namespace Persium\Station\Domain\CommandBus\GetLatestDataOneStation;

use Carbon\Carbon;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;

class GetLatestDataOneStationHandler
{
    public function __construct(
        private readonly StationDataCacheInterface $station_data_cache,
        private readonly StationRepositoryInterface $station_repository,
    )
    {
    }

    public function handle(
        GetLatestDataOneStationCommand $command,
    ) : ?StationDataVO
    {

        //TODO: check cache first

        $station = $command->station;
        $latest_data = $this->station_repository->getLatestDataByStationId($station->getId());
        if (empty($latest_data)) {
            return null;
        }

        return $this->formatDataToVO($station, $latest_data);
    }

    protected function formatDataToVO(Station $station, array $data): StationDataVO
    {
        $timestamp = end($data)['timestamp'];
        $timestamp = Carbon::createFromTimeString($timestamp);
        $sensor_data_vos = [];
        foreach ($data as $datum) {
            $sensor_data_vos[] = new StationSensorDataVO(
                timestamp: $timestamp,
                name: $datum['name'],
                value: $datum['value'],
                ugm3: $datum['ugm3'],
                ppb: $datum['ppb'],
                unit: $datum['unit'],
                daqi: null,
                caqi: null,
                usaqi: null,
            );
        }

        return new StationDataVO(
            station: $station,
            timestamp: $timestamp,
            sensor_data_vos: $sensor_data_vos,
            caqi: null,
            caqi_factor: '',
            usaqi: null,
            usaqi_factor: '',
            daqi: null,
            daqi_factor: '',
        );
    }
}
