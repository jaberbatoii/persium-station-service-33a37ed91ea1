<?php

namespace Persium\Station\Domain\DTOs\Response;

class StationInBoundaryResponseDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly int $aqi,
    )
    {
    }
}
