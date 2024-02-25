<?php

namespace Persium\Station\Domain\DTOs\Response;

use Persium\Station\Domain\Entities\SensorType\SensorTypeRepositoryInterface;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;

class StationLatestDataResponseDTO
{
    public function __construct(
        public ?int $daqi,
        public ?int $caqi,
        public ?int $usaqi,
        public string $timestamp,
        public array $pollutants,
        public array $atmospherics,
        public array $engineerings,
    )
    {
    }
}
