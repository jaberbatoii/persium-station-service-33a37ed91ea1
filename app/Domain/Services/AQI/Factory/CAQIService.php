<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Services\AQI\Factory;

class CAQIService implements AQIServiceInterface
{
    public function calculatePM25AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 25, 'max_value' => 15],
            ['aqi' => 50, 'max_value' => 30],
            ['aqi' => 75, 'max_value' => 55],
            ['aqi' => 100, 'max_value' => 110],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculatePM10AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 25, 'max_value' => 25],
            ['aqi' => 50, 'max_value' => 50],
            ['aqi' => 75, 'max_value' => 90],
            ['aqi' => 100, 'max_value' => 180],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateO3AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 25, 'max_value' => 60],
            ['aqi' => 50, 'max_value' => 120],
            ['aqi' => 75, 'max_value' => 180],
            ['aqi' => 100, 'max_value' => 240],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateNO2AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 25, 'max_value' => 50],
            ['aqi' => 50, 'max_value' => 100],
            ['aqi' => 75, 'max_value' => 200],
            ['aqi' => 100, 'max_value' => 400],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateSO2AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 25, 'max_value' => 50],
            ['aqi' => 50, 'max_value' => 100],
            ['aqi' => 75, 'max_value' => 350],
            ['aqi' => 100, 'max_value' => 500],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateCOAQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 25, 'max_value' => 5000],
            ['aqi' => 50, 'max_value' =>7500],
            ['aqi' => 75, 'max_value' => 10000],
            ['aqi' => 100, 'max_value' => 20000],
        ];

        return $this->calculate($value, $ranges);
    }

    public function calculate(float $value, array $ranges): int
    {
        if ($value < 0) {
            return -1;
        }

        for ($i = 0; $i < count($ranges); $i++) {
            $max_aqi = $ranges[$i]['aqi'];
            $max_value = $ranges[$i]['max_value'];

            if ($value <= $max_value) {
                $lower_max_aqi = $ranges[$i - 1]['aqi'];
                $lower_max_value = $ranges[$i - 1]['max_value'];

                $ratio = ($max_value - $lower_max_value) / ($max_aqi - $lower_max_aqi);

                $additional_aqi = ($value - $lower_max_value) / $ratio;

                // Round up aqi value to next integer
                return (int)ceil($lower_max_aqi + $additional_aqi);
            }
        }

        $max_aqi_object = end($ranges);
        $max_aqi = $max_aqi_object['aqi'];
        $max_value = $max_aqi_object['max_value'];

        $ratio = $max_value / $max_aqi;

        $additional_aqi = ($value - $max_value) / $ratio;

        // Round up aqi value to next integer
        return (int) ceil($max_aqi + $additional_aqi);
    }
}
