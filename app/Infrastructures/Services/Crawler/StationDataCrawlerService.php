<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Exception;
use Persium\Station\Dictionary\AQIDictionary;
use Persium\Station\Dictionary\UnitDictionary;
use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationAQIData;
use Persium\Station\Domain\Entities\Station\StationAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensor;
use Persium\Station\Domain\Entities\Station\StationSensorAQIData;
use Persium\Station\Domain\Entities\Station\StationSensorAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorData;
use Persium\Station\Domain\Entities\Station\StationSensorDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;

class StationDataCrawlerService
{
    const BATCH_SIZE = 100;

    public function __construct(
        private readonly CrawlerInterface                        $crawler,
        private readonly StationRepositoryInterface              $station_repository_interface,
        private readonly SensorTypeRepositoryInterface           $sensor_type_repository_interface,
        private readonly StationDataCacheInterface               $station_data_cache_interface,
        private readonly EntityManager                           $entity_manager,
    ) {

    }

    private function getSensorType(string $name): SensorType
    {
        $sensor_type = $this->sensor_type_repository_interface->findOneByName($name);
        if (is_null($sensor_type)) {
            $sensor_type = new SensorType(
                raw_name: $name,
                name: $name,
                display_name: $name,
                type: 1,
                e_multi: 0,
                e_pow_multi: 0,
                m_multi: 0,
                molar_mass: 0,
                constant: 0,
                p_nom: 0,
            );
            $this->sensor_type_repository_interface->save($sensor_type);
        }

        return $sensor_type;
    }

    private function getSensor(Station $station, SensorType $sensor_type): StationSensor
    {
        $sensor = $this->station_repository_interface->findOneSensorByType($station, $sensor_type);
        if (is_null($sensor)) {
            $sensor = new StationSensor(
                station: $station,
                type: $sensor_type,
                vendor: null,
                name: $sensor_type->getRawName(),
                unit: UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
                location: 1,
                status: 1,
                sr1: null,
                sr2: null,
                ar1: null,
                ar2: null,
                sensitivity: null,
                second_sensitivity: null,
                vp_code: null,
                aux_base: null,
                sensor_base: null,
                installed_at: Carbon::now()
            );
            $station->addSensor($sensor);
            $this->station_repository_interface->save($station);
        }
        return $sensor;
    }

    private function saveDataToDatabase(StationDataVO $station_data_vo): void
    {
        $timestamp = $station_data_vo->getTimestamp();
        $station = $station_data_vo->getStation();
        $daqi = $station_data_vo->getDaqi();
        $daqi_factor = $station_data_vo->getDaqiFactor();
        $caqi = $station_data_vo->getCAQI();
        $caqi_factor = $station_data_vo->getCaqiFactor();
        $usaqi = $station_data_vo->getUsaqi();
        $usaqi_factor = $station_data_vo->getUsaqiFactor();

        if (is_numeric($daqi) && $daqi > 1){
            $station->addAQIData(new StationAQIData(
                station: $station,
                value: $daqi,
                type: AQIDictionary::TYPE_DAQI,
                factor: $daqi_factor,
                timestamp: $timestamp,
            ));
        }

        if (is_numeric($caqi) && $caqi >= 0){
            $station->addAQIData(new StationAQIData(
                station: $station,
                value: $caqi,
                type: AQIDictionary::TYPE_CAQI,
                factor: $caqi_factor,
                timestamp: $timestamp,
            ));
        }

        if (is_numeric($usaqi) && $usaqi >= 0){
            $station->addAQIData(new StationAQIData(
                station: $station,
                value: $usaqi,
                type: AQIDictionary::TYPE_USAQI,
                factor: $usaqi_factor,
                timestamp: $timestamp,
            ));
        }
        $sensor_data_vos = $station_data_vo->getSensorDataVOs();
        foreach ($sensor_data_vos as $sensor_data_vo) {
            if (!$sensor_data_vo instanceof StationSensorDataVO){
                throw new Exception('Invalid StationSensorDataVO provided');
            }
            $name = $sensor_data_vo->getName();
            $value = $sensor_data_vo->getValue();
            $ugm3 = $sensor_data_vo->getUGM3();
            $ppb   = $sensor_data_vo->getPPB();
            $sensor_type = $this->getSensorType($name);
            $sensor = $this->getSensor($station, $sensor_type);
            $station->addSensorData($sensor, new StationSensorData(
                sensor: $sensor,
                timestamp: $timestamp,
                ugm3: $ugm3,
                value: $value,
                ppb: $ppb,
            ));

            $daqi = $sensor_data_vo->getDaqi();
            if ($daqi > 1){
                $station->addSensorAQIData($sensor, new StationSensorAQIData(
                    sensor: $sensor,
                    value: $daqi,
                    type: AQIDictionary::TYPE_DAQI,
                    timestamp: $timestamp,
                ));
            }
            if ($caqi > 1){
                $station->addSensorAQIData($sensor, new StationSensorAQIData(
                    sensor: $sensor,
                    value: $caqi,
                    type: AQIDictionary::TYPE_CAQI,
                    timestamp: $timestamp,
                ));
            }

            if ($usaqi > 1){
                $station->addSensorAQIData($sensor, new StationSensorAQIData(
                    sensor: $sensor,
                    value: $usaqi,
                    type: AQIDictionary::TYPE_USAQI,
                    timestamp: $timestamp,
                ));
            }
        }
    }

    private function saveDataToRedis(StationDataVO $station_data_vo): void
    {
        return;
    }

    private function saveLatestDataToRedis(StationDataVO $station_data_vo): void
    {
        $this->station_data_cache_interface->saveLatestData($station_data_vo);
    }

    public function crawlDataOneStationInRange(Station $station, Carbon $start_time, Carbon $end_time): void
    {
        $batch_size = self::BATCH_SIZE;
        $batch_count = 0;

        $timeframes = $this->crawler->getDataInTimeRangeOneStation($station, $start_time, $end_time);
        foreach ($timeframes as $timeframe) {
            if (!$timeframe instanceof StationDataVO) {
                throw new Exception('Invalid data type');
            }

            $this->saveDataToDatabase($timeframe);

            $batch_count++;
            if ($batch_count !== 0 & $batch_count % $batch_size === 0) {
                $this->entity_manager->flush();
                $batch_count = 0;
            }
        }

        if ($batch_count !== 0) {
            $this->entity_manager->flush();
        }
    }

    public function crawlLatestDataOneStation(Station $station): void
    {
        $latest_timestamp = $this->station_data_cache_interface->getLatestTimestamp($station->getID());
        $batch_size = self::BATCH_SIZE;
        $batch_count = 0;
        $latest_timestamp = $latest_timestamp ?? Carbon::now()->subYear();
        $timeframes = $this->crawler->getLatestDataOneStation($station);
        foreach ($timeframes as $timeframe) {
            $timestamp = $timeframe->getTimestamp();
            if ($latest_timestamp->greaterThanOrEqualTo($timestamp)) {
                continue;
            }

            if (!$timeframe instanceof StationDataVO) {
                throw new Exception('Invalid data type');
            }

            if ($this->crawler->canSaveLatestDataToDatabase()) {
                $this->saveDataToDatabase($timeframe);
                $batch_count++;
            }

            $this->saveDataToRedis($timeframe);

            if ($batch_count !== 0 & $batch_count % $batch_size === 0) {
                $this->entity_manager->flush();
                $batch_count = 0;
            }
        }

        $latest_data = end($timeframes);
        if (!$latest_data instanceof StationDataVO) {
            throw new Exception('Invalid data type');
        }
        $this->saveLatestDataToRedis($latest_data);

        if ($batch_count !== 0) {
            $this->entity_manager->flush();
        }
    }

}
