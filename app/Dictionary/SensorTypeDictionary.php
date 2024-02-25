<?php

namespace Persium\Station\Dictionary;

class SensorTypeDictionary
{
    const HUMIDITY = 'humidity';
    const TEMPERATURE = 'temperature';
    const PRESSURE = 'pressure';
    const WIND_SPEED = 'wind_speed';
    const WIND_DIRECTION = 'wind_direction';
    const PM2_5 = 'PM2.5';
    const PM1 = 'PM1';
    const PM10 = 'PM10';
    const O3 = 'O3';
    const NO2 = 'NO2';
    const SO2 = 'SO2';
    const CO = 'CO';
    const CO2 = 'CO2';
    const NO = 'NO';
    const NH3 = 'NH3';
    const VOC = 'VOC';
    const NOX = 'NOX';

    const RAW_NAME_TO_NAME_MAPPING = [
        'HUM' => self::HUMIDITY,
        'TEMP' => self::TEMPERATURE,
        'AIRPRES' => self::PRESSURE,
        'ws'    => self::WIND_SPEED,
        'wd'    => self::WIND_DIRECTION,
        'hum'   => self::HUMIDITY,
        'airpres' => self::PRESSURE,
        'air_temp' => self::TEMPERATURE,
        'air_pres' => self::PRESSURE,
        'airtemp' => self::TEMPERATURE,
        'NOx' => self::NOX,
        'pm1.0' => self::PM1,
        'pm2.5' => self::PM2_5,
        'PM-2-5' => self::PM2_5,
        'PM-10' => self::PM10,
        'pm10.0' => self::PM10,

    ];

    const DEFAULT_TEMPERATURE = 25;
    const DEFAULT_PRESSURE = 1013.25;

    const MOLE_WEIGHTS= [
        self::O3    => 47.998,
        self::NO2   => 46.0055,
        self::NO    => 30.006,
        self::CO    => 28.011,
        self::SO2   => 64.06,
        self::NH3   => 17.0306,
    ];
}
