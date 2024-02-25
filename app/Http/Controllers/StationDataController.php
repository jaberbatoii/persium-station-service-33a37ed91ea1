<?php

namespace Persium\Station\Http\Controllers;

use Joselfonseca\LaravelTactician\CommandBusInterface;
use Persium\Station\Domain\CommandBus\GetLatestDataOneStation\GetLatestDataOneStationCommand;
use Persium\Station\Domain\CommandBus\GetLatestDataOneStation\GetLatestDataOneStationHandler;
use Persium\Station\Domain\DTOs\Response\StationLatestDataResponseDTO;
use Persium\Station\Domain\DTOs\Response\StationSensorDataDTO;
use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;

class StationDataController extends BaseAPIController
{
    public function __construct(
        private CommandBusInterface $bus,
        private StationRepositoryInterface $station_repository,
        private SensorTypeRepositoryInterface $sensor_type_repository,
    )
    {
    }

    public function getLatestDataOneStation(string $uuid)
    {
        $station = $this->station_repository->findOneByUuid($uuid);
        if (!$station) {
            return $this->responseBadRequest('Station not found');
        }

        $this->bus->addHandler(GetLatestDataOneStationCommand::class, GetLatestDataOneStationHandler::class);

        $station_data_vo = $this->bus->dispatch(new GetLatestDataOneStationCommand($station));
        if (!$station_data_vo) {
            return $this->responseBadRequest('No data found');
        }

        $sensor_data_vos = $station_data_vo->getSensorDataVOs();

        $pollutants = [];
        $atmospherics = [];
        $engineerings = [];
        foreach ($sensor_data_vos as $sensor_data_vo) {
            /** @var StationSensorDataVO $sensor_data_vo */
            $name = $sensor_data_vo->getName();
            $sensor_type = $this->sensor_type_repository->findOneByName($name);
            $sensor_data = new StationSensorDataDTO(
                name: $sensor_data_vo->getName(),
                unit: $sensor_data_vo->getUnit(),
                value: $sensor_data_vo->getValue(),
                color: '',
                percentage: null
            );
            if($sensor_type->isPollutant()){
                $pollutants[] = $sensor_data;
            }
            if($sensor_type->isAtmospheric()){
                $atmospherics[] = $sensor_data;
            }
            if($sensor_type->isEngineering()){
                $engineerings[] = $sensor_data;
            }
        }

        $result = new StationLatestDataResponseDTO(
            daqi: $station_data_vo->getDAQI(),
            caqi: $station_data_vo->getCAQI(),
            usaqi: $station_data_vo->getUSAQI(),
            timestamp: $station_data_vo->getTimestamp()->format('Y-m-d H:i:s'),
            pollutants: $pollutants,
            atmospherics: $atmospherics,
            engineerings: $engineerings,
        );

        return response()->json($result);
    }
}
