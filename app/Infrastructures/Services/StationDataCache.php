<?php
namespace Persium\Station\Infrastructures\Services;

use DateTimeInterface;
use Persium\Station\Dictionary\AQIDictionary;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Domain\ValueObjects\StationAQIDataVO;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Infrastructures\Persistency\Redis\Repositories\StationDataRepository;

class StationDataCache implements StationDataCacheInterface
{
    public function __construct(
        private readonly StationRepositoryInterface $station_repository_interface,
        private readonly StationDataRepository $station_data_repository,
    )
    {
    }

    public function getLatestTimestamp(int $station_id): ?DateTimeInterface
    {
        $timestamp = $this->station_data_repository->getLatestTimestamp($station_id);
        if ($timestamp) {
            return $timestamp;
        }

        $latest_timestamp = $this->station_repository_interface->getLatestDataTime($station_id);

        if ($latest_timestamp){
            return $latest_timestamp;
        }

        return null;
    }

    public function saveLatestData(StationDataVO $station_data_vo): void
    {
        $this->station_data_repository->saveLatestData($station_data_vo);
    }

    public function getLatestData(int $station_id): ?StationDataVO
    {
        return $this->station_data_repository->getLatestData($station_id);
    }

    public function getLatestAQIData(int $station_id, int $aqi_type): ?StationAQIDataVO
    {
        $latest_data = $this->station_data_repository->getLatestData($station_id);
        if ($latest_data) {
            $aqi_value = null;
            $aqi_factor = null;
            switch ($aqi_type) {
                case AQIDictionary::TYPE_DAQI:
                    $aqi_value = $latest_data->getDAQI();
                    $aqi_factor = $latest_data->getDAQIFactor();
                    break;
                case AQIDictionary::TYPE_CAQI:
                    $aqi_value = $latest_data->getCAQI();
                    $aqi_factor = $latest_data->getCAQIFactor();
                    break;
                case AQIDictionary::TYPE_USAQI:
                    $aqi_value = $latest_data->getUSAQI();
                    $aqi_factor = $latest_data->getUSAQIFactor();
                    break;
            }

            if ($aqi_value && $aqi_factor) {
                return new StationAQIDataVO(
                    value: $aqi_value,
                    type: $aqi_type,
                    factor: $aqi_factor,
                    timestamp: $latest_data->getTimestamp()
                );
            }
        }

        $latest_aqi_data = $this->station_repository_interface->getLatestAQIDataByAQIType($station_id, $aqi_type);
        if ($latest_aqi_data) {
            return new StationAQIDataVO(
                value: $latest_aqi_data->getValue(),
                type: $latest_aqi_data->getType(),
                factor: $latest_aqi_data->getFactor(),
                timestamp: $latest_aqi_data->getTimestamp()
            );
        }
        return null;
    }
}
