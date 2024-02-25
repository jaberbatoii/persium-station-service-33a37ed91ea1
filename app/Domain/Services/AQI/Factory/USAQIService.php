<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Services\AQI\Factory;

class USAQIService implements AQIServiceInterface
{
    public function calculatePM25AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 50, 'max_value' => 12],
            ['aqi' => 100, 'max_value' => 35.4],
            ['aqi' => 150, 'max_value' => 55.4],
            ['aqi' => 200, 'max_value' => 150.4],
            ['aqi' => 300, 'max_value' => 250.4],
            ['aqi' => 400, 'max_value' => 350.4],
            ['aqi' => 500, 'max_value' => 500.4],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculatePM10AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 50, 'max_value' => 54],
            ['aqi' => 100, 'max_value' => 154],
            ['aqi' => 150, 'max_value' => 254],
            ['aqi' => 200, 'max_value' => 354],
            ['aqi' => 300, 'max_value' => 424],
            ['aqi' => 400, 'max_value' => 504],
            ['aqi' => 500, 'max_value' => 604],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateO3AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 50, 'max_value' => 44],
            ['aqi' => 100, 'max_value' => 84],
            ['aqi' => 150, 'max_value' => 124],
            ['aqi' => 200, 'max_value' => 164],
            ['aqi' => 300, 'max_value' => 204],
            ['aqi' => 400, 'max_value' => 404],
            ['aqi' => 500, 'max_value' => 504],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateNO2AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 50, 'max_value' => 53],
            ['aqi' => 100, 'max_value' => 100],
            ['aqi' => 150, 'max_value' => 360],
            ['aqi' => 200, 'max_value' => 649],
            ['aqi' => 300, 'max_value' => 1249],
            ['aqi' => 400, 'max_value' => 1649],
            ['aqi' => 500, 'max_value' => 2049],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateSO2AQI(float $value): int
    {
        $ranges = [
            ['aqi' => 0, 'max_value' => 0],
            ['aqi' => 50, 'max_value' => 35],
            ['aqi' => 100, 'max_value' => 75],
            ['aqi' => 150, 'max_value' => 185],
            ['aqi' => 200, 'max_value' => 304],
            ['aqi' => 300, 'max_value' => 604],
            ['aqi' => 400, 'max_value' => 804],
            ['aqi' => 500, 'max_value' => 1004],
        ];
        return $this->calculate($value, $ranges);
    }

    public function calculateCOAQI(float $value): int
    {
        return -1;
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
