<?php

namespace Persium\Station\Domain\Services\SensorType;

use Persium\Station\Dictionary\SensorTypeDictionary;

class SensorTypeDictionaryService
{
    public function convertRawNameToName(string $raw_name): string
    {
        if (array_key_exists($raw_name, SensorTypeDictionary::RAW_NAME_TO_NAME_MAPPING) === false) {
            return $raw_name;
        }

        return SensorTypeDictionary::RAW_NAME_TO_NAME_MAPPING[$raw_name];
    }
}
