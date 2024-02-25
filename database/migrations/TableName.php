<?php
namespace Database\Migrations;

class TableName
{
    public const TABLE_STATION = 'stations';
    public const TABLE_SENSOR = 'station_sensors';
    public const TABLE_VENDOR = 'vendors';
    public const TABLE_SENSOR_TYPE = 'sensor_types';
    public const TABLE_SENSOR_DATA = 'station_sensor_data';
    public const TABLE_AQI_DATA = 'station_aqi_data';
    public const TABLE_SENSOR_AQI_DATA = 'station_sensor_aqi_data';
    public const TABLE_CROSS_SENSITIVITY = 'cross_sensitivities';
    public const TABLE_FAILED_JOB = 'failed_jobs';
    public const TABLE_WIND_POINTS = 'wind_points';
    public const TABLE_WIND_DATA = 'wind_data';
}
