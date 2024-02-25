<?php

namespace Persium\Station\Domain\DTOs\Response;

class StationSensorDataDTO
{
    public function __construct(
        public string   $name,
        public string   $unit,
        public ?float   $value,
        public ?string  $color,
        public ?float   $percentage,
    )
    {
    }
}
