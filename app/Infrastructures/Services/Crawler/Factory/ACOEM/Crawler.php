<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler\Factory\ACOEM;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Persium\Station\Dictionary\AQIDictionary;
use Persium\Station\Dictionary\SensorTypeDictionary;
use Persium\Station\Dictionary\UnitDictionary;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Services\AQI\AQIService;
use Persium\Station\Domain\Services\SensorType\SensorTypeDictionaryService;
use Persium\Station\Domain\Services\Unit\UnitConversionService;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;
use Persium\Station\Domain\ValueObjects\StationVO;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerBase;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;

class Crawler extends CrawlerBase implements CrawlerInterface
{
    const UOB_BAQS_STATION_SOURCE_ID= 1138100;
    const LATEST_DATA_NUMBER_OF_RECORDS = 100;
    protected string $config_name = 'ACOEM';

    public function getStations(): array
    {
        $result = [];
        $url = $this->base_url . "/4.0/GET/devices";
        $response = Http::withHeaders([
            "AccountId" => $this->auth_user,
            "LicenceKey" => $this->auth_password,
        ])->get($url);
        $items = $response->json();

        foreach ($items as $item) {
            $station_vo = new StationVO(
                name: $item['DeviceName'],
                address: '',
                source: $this->child_sources[0],
                source_id: strval($item['UniqueId']),
                latitude: $item['Latitude'],
                longitude: $item['Longitude'],
                altitude: floatval($item['Altitude']),
                photo_url: null,
                installed_at: null,
            );

            $result[] = $station_vo;
        }

        return $result;
    }

    public function getDataInTimeRangeOneStation(Station $station, Carbon $start_time, Carbon $end_time): array
    {
        $result = [];
        $start_time = $start_time->format('Y-m-d').'T'.$start_time->format('H:i:s');
        $end_time = $end_time->format('Y-m-d').'T'.$end_time->format('H:i:s');

        $url = $this->base_url . "/4.0/GET/devicedata/{$start_time}/{$end_time}/{$station->getSourceID()}";
        $response = Http::withHeaders([
            "AccountId" => $this->auth_user,
            "LicenceKey" => $this->auth_password,
        ])->timeout(300)->get($url);

        $items = $response->json();

        if (empty($items)) {
            return $result;
        }

        if ($station->getSourceID() == self::UOB_BAQS_STATION_SOURCE_ID) {
            $items = $this->ignoreToCaptureData($items);
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                $result[] = $this->transformOneDataToVO($station, $item);
            }
        }

        return $result;
    }

    public function getLatestDataOneStation(Station $station): array
    {
        $result = [];
        $number_of_records = self::LATEST_DATA_NUMBER_OF_RECORDS;
        $url = $this->base_url . "/4.0/GET/devicedata/latest/{$number_of_records}/{$station->getSourceID()}";
        $response = Http::withHeaders([
            "AccountId" => $this->auth_user,
            "LicenceKey" => $this->auth_password,
        ])->get($url);

        $items = $response->json();
        foreach ($items as $item) {
            $result[] = $this->transformOneDataToVO($station, $item);
        }

        return array_reverse($result);
    }

    protected function ignoreToCaptureData(array $data): array
    {
        $result = [];
        foreach ($data as $item) {
            $time = Carbon::createFromTimestamp($item['Timestamp']['Timestamp']);
            $start_time = $time->clone()->startOfDay()->setHour(1)->setMinute(20);
            $end_time = $time->clone()->startOfDay()->setHour(23)->setMinute(50);
            if ($time->lt($start_time) || $time->gt($end_time)) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function transformOneDataToVO(Station $station, array $data): StationDataVO
    {
        $daqi_service = AQIService::getAQIService(AQIDictionary::DAQI);
        $caqi_service = AQIService::getAQIService(AQIDictionary::CAQI);
        $usaqi_service = AQIService::getAQIService(AQIDictionary::USAQI);

        $unit_conversion_service = new UnitConversionService();
        $sensor_type_dictionary_service = new SensorTypeDictionaryService();
        $collected_data = [];
        $timestamp = Carbon::parse($data['Timestamp']['Timestamp']);
        $items = $data['Channels'];

        $temperature = SensorTypeDictionary::DEFAULT_TEMPERATURE;
        $pressure = SensorTypeDictionary::DEFAULT_PRESSURE;

        foreach ($items as $item) {
            $value = $item['Scaled']['Reading'];
            //Ignore invalid data
            if (!empty(data_get($item, 'Scaled.Flags')) || is_null($value)) {
                continue;
            }

            $sensor_raw_name = $item['SensorLabel'];
            $sensor_name = $sensor_type_dictionary_service->convertRawNameToName($sensor_raw_name);

            if (array_key_exists($sensor_name, $collected_data)) {
                continue;
            }
            $unit = $item['UnitName'];
            list ($unit, $value) = $this->convertToStandard($unit, $value);


            if ($sensor_name == SensorTypeDictionary::TEMPERATURE) {
                $temperature = $value;
            }

            if ($sensor_name == SensorTypeDictionary::PRESSURE) {
                $pressure = $value;
            }

            $collected_data[$sensor_name] = [
                'value' => $value,
                'unit' => $unit,
            ];
        }

        $daqi = null;
        $daqi_factor = '';
        $caqi = null;
        $caqi_factor = '';
        $usaqi = null;
        $usaqi_factor = '';
        $sensor_items = [];
        foreach ($collected_data as $sensor_type_name => $value) {
            $unit = $value['unit'];
            $value = $value['value'];
            $ppb = null;
            $ugm3 = null;
            $sensor_daqi = null;
            $sensor_caqi = null;
            $sensor_usaqi = null;
            $mole_weight = SensorTypeDictionary::MOLE_WEIGHTS[strtoupper($sensor_type_name)] ?? null;
            if ($unit === UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER){
                $ugm3 = $value;

                if ($mole_weight){
                    $ppb = $unit_conversion_service->ugm3ToPpb($value, $mole_weight, $temperature, $pressure);
                }
            } elseif ($unit === UnitDictionary::UNIT_PARTS_PER_BILLION) {
                $ppb = $value;
                if ($mole_weight){
                    $ugm3 = $unit_conversion_service->ppbToUgm3($value, $mole_weight, $temperature, $pressure);
                }
            }

            if ($ugm3){
                $sensor_daqi = $daqi_service->calculateAQI(
                    $sensor_type_name,
                    $ugm3
                );
                if ($sensor_daqi > $daqi) {
                    $daqi = $sensor_daqi;
                    $daqi_factor = $sensor_type_name;
                }

                $caqi_temp = $caqi_service->calculateAQI(
                    $sensor_type_name,
                    $ugm3
                );
                if ($caqi_temp > $caqi) {
                    $caqi = $caqi_temp;
                    $caqi_factor = $sensor_type_name;
                }
            }

            if ($ppb){
                $sensor_usaqi = $usaqi_service->calculateAQI(
                    $sensor_type_name,
                    $ppb
                );
                if ($sensor_usaqi > $usaqi) {
                    $usaqi = $sensor_usaqi;
                    $usaqi_factor = $sensor_type_name;
                }
            }

            $sensor_items[] = new StationSensorDataVO(
                timestamp: $timestamp,
                name: $sensor_type_name,
                value: $value,
                ugm3: $ugm3,
                ppb: $ppb,
                unit: $unit,
                daqi: $sensor_daqi,
                caqi: $sensor_caqi,
                usaqi: $sensor_usaqi,
            );
        }

        return new StationDataVO(
            station: $station,
            timestamp: $timestamp,
            sensor_data_vos: $sensor_items,
            caqi: $caqi,
            caqi_factor: $caqi_factor,
            usaqi: $usaqi,
            usaqi_factor: $usaqi_factor,
            daqi: $daqi,
            daqi_factor: $daqi_factor,
        );
    }

    private function convertToStandard(string $unit, float $value): array
    {
        //https://api.airmonitors.net/4.0/GET/units
        switch ($unit) {
            case 'nanogramme per meter cubed':
                return [UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER, $value * 0.001];
            case 'Pressure (bar)':          // bar to hPa
                return [UnitDictionary::UNIT_HECTOPASCAL, $value * 1000];
            case 'Milligrams Per Cubic Meter':  // mg/m3 to ug/m3
                return [UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER, $value * 1000];
            case 'Micrograms Per Cubic Meter':
                return [UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER, $value];
            case 'Hectopascals':
                return [UnitDictionary::UNIT_HECTOPASCAL, $value];
            case 'Percent':
                return [UnitDictionary::UNIT_PERCENT, $value];
            case 'Celsius':
                return [UnitDictionary::UNIT_CELSIUS, $value];
            case 'Degrees':
                return [UnitDictionary::UNIT_DEGREE, $value];
            case 'Volts':
                return [UnitDictionary::UNIT_VOLT, $value];
            case 'Pressure (mbar)':
                return [UnitDictionary::UNIT_HECTOPASCAL, $value * 10];
            case 'Parts Per Billion':
                return [UnitDictionary::UNIT_PARTS_PER_BILLION, $value];
            case 'Parts Per Million':
                return [UnitDictionary::UNIT_PARTS_PER_BILLION, $value * 1000];
            case 'Miles Per Hour':
                return [UnitDictionary::UNIT_METER_PER_SECOND, $value / 2.23694];
            case 'Particles per cm3':
                return [UnitDictionary::UNIT_PARTICLE_PER_CUBIC_METER, $value];
        }
        throw new \Exception('Unknown unit: '.$unit);
    }

    public function canSaveLatestDataToDatabase(): bool
    {
        return true;
    }
}
