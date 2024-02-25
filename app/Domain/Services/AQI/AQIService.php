<?php

namespace Persium\Station\Domain\Services\AQI;

use Persium\Station\Dictionary\AQIDictionary;
use Persium\Station\Dictionary\SensorTypeDictionary;
use Persium\Station\Domain\Services\AQI\Factory\AQIServiceInterface;
use Persium\Station\Domain\Services\AQI\Factory\CAQIService;
use Persium\Station\Domain\Services\AQI\Factory\DAQIService;
use Persium\Station\Domain\Services\AQI\Factory\USAQIService;

class AQIService
{
    public function __construct(
        private AQIServiceInterface $aqi_service_interface,
    )
    {
    }

    public static function getAQIService(string $aqi_type): self
    {
        $aqi_type = strtolower($aqi_type);
        return match ($aqi_type) {
            AQIDictionary::DAQI => new self(new DAQIService()),
            AQIDictionary::CAQI => new self(new CAQIService()),
            AQIDictionary::USAQI => new self(new USAQIService()),
            default => throw new \Exception('AQI type not found'),
        };
    }

    public function calculateAQI(string $pollutant_name, float $value): int
    {
        $pollutant_name = strtoupper($pollutant_name);
        return match ($pollutant_name) {
            SensorTypeDictionary::PM2_5 => $this->aqi_service_interface->calculatePM25AQI($value),
            SensorTypeDictionary::PM10 => $this->aqi_service_interface->calculatePM10AQI($value),
            SensorTypeDictionary::O3 => $this->aqi_service_interface->calculateO3AQI($value),
            SensorTypeDictionary::NO2 => $this->aqi_service_interface->calculateNO2AQI($value),
            SensorTypeDictionary::SO2 => $this->aqi_service_interface->calculateSO2AQI($value),
            SensorTypeDictionary::CO => $this->aqi_service_interface->calculateCOAQI($value),
            default => -1,
        };
    }
}
