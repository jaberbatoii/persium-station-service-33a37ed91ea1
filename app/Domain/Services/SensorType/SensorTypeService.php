<?php

namespace Persium\Station\Domain\Services\SensorType;

class SensorTypeService
{
    public function isPollutant(string $name){
        return true;
    }

    public function isAtmospheric(string $name){
        return true;
    }

    public function isEngineering(string $name){
        return true;
    }


    public function getSensorTypeByName(){

    }
}
