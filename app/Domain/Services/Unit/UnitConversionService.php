<?php

namespace Persium\Station\Domain\Services\Unit;

use Persium\Station\Dictionary\UnitDictionary;

class UnitConversionService
{
    public function ugm3ToPpb(float $ugm3, float $mole_weight, float $temperature, float $pressure) : float
    {
        if ($pressure == 0){
            $pressure = 1013;         // set to ISO pressure
            $temperature = 20;        // set to ISO temperature
        }

        $mole_volume = 22.41 * (($temperature+273) / 273) * 1013 / $pressure;
        return $ugm3 * $mole_volume / $mole_weight;
    }

    public function ppbToUgm3(float $ppb, float $mole_weight, float $temperature, float $pressure): float
    {
        if ($pressure == 0){
            $pressure = 1013;         // set to ISO pressure
            $temperature = 20;        // set to ISO temperature
        }

        $mole_volume = 22.41 * (($temperature+273) / 273) * 1013 / $pressure;

        return $ppb * $mole_volume / $mole_weight;
    }

    // standard conversion
    public function convert(float $value, string $from_unit, string $to_unit): float
    {
        switch ($from_unit) {
            case UnitDictionary::UNIT_FAHRENHEIT:
                switch ($to_unit) {
                    case UnitDictionary::UNIT_CELSIUS:
                        return ($value - 32) * 5 / 9;
                    case UnitDictionary::UNIT_KELVIN:
                        return ($value - 32) * 5 / 9 + 273.15;
                }
                break;
            case UnitDictionary::UNIT_CELSIUS:
                switch ($to_unit) {
                    case UnitDictionary::UNIT_FAHRENHEIT:
                        return $value * 9 / 5 + 32;
                    case UnitDictionary::UNIT_KELVIN:
                        return $value + 273.15;
                }
                break;
        }

        throw new \Exception('Invalid unit conversion from ' . $from_unit . ' to ' . $to_unit . '.');
    }
}
