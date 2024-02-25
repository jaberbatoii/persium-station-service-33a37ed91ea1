<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Services\AQI\Factory;

interface AQIServiceInterface
{
    public function calculatePM25AQI(float $value): int;
    public function calculatePM10AQI(float $value): int;
    public function calculateO3AQI(float $value): int;
    public function calculateNO2AQI(float $value): int;
    public function calculateSO2AQI(float $value): int;
    public function calculateCOAQI(float $value): int;
}
