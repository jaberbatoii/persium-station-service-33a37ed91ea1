<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Services\AQI\Factory;

class DAQIService implements AQIServiceInterface
{
    public function calculatePM25AQI(float $value): int
    {
        $ranges = [
            1 => 12,
            2 => 24,
            3 => 36,
            4 => 42,
            5 => 48,
            6 => 54,
            7 => 59,
            8 => 65,
            9 => 71,
            10 => 90,
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculatePM10AQI(float $value): int
    {
        $ranges = [
            1 => 17,
            2 => 34,
            3 => 51,
            4 => 59,
            5 => 67,
            6 => 76,
            7 => 84,
            8 => 92,
            9 => 101,
            10 => 150,
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateO3AQI(float $value): int
    {
        $ranges = [
            1 => 34,
            2 => 67,
            3 => 101,
            4 => 121,
            5 => 141,
            6 => 161,
            7 => 188,
            8 => 214,
            9 => 241,
            10 => 300,
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateNO2AQI(float $value): int
    {
        $ranges = [
            1 => 68,
            2 => 135,
            3 => 201,
            4 => 268,
            5 => 335,
            6 => 401,
            7 => 468,
            8 => 535,
            9 => 601,
            10 => 650,
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateSO2AQI(float $value): int
    {
        $ranges = [
            1 => 89,
            2 => 178,
            3 => 267,
            4 => 355,
            5 => 444,
            6 => 533,
            7 => 711,
            8 => 888,
            9 => 1065,
            10 => 1200,
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateCOAQI(float $value): int
    {
        return -1;
    }

    public function calculate(float $value, array $ranges): int
    {
        $result = 10;
        foreach ($ranges as $index => $max_value) {
            if ($value <= $max_value) {
                return $index;
            }
        }
        return $result;
    }
}
