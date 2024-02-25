<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler\Factory\PurpleAir;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Persium\Station\Dictionary\AQIDictionary;
use Persium\Station\Dictionary\SensorTypeDictionary;
use Persium\Station\Dictionary\UnitDictionary;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Services\AQI\AQIService;
use Persium\Station\Domain\Services\Unit\UnitConversionService;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;
use Persium\Station\Domain\ValueObjects\StationVO;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerBase;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;
use function Persium\Station\Domain\Services\Crawler\Factory\PurpleAir\count;

class Crawler extends CrawlerBase implements CrawlerInterface
{
    protected string $config_name = 'PurpleAir';
    const STATION_FIELDS = [
        'name',
        'latitude',
        'longitude',
        'altitude',
        'date_created'
    ];
    const FIELD_MAPPING = [
        'sensor_index' => 'source_id',
        'date_created' => 'installed_date'
    ];
    const DATA_FIELDS = [
        'sensor_index',
        'last_seen',
        'pm10.0',
        'pm1.0',
        'pm2.5',
        'humidity',
        'temperature',
        'pressure',
        'ozone1',
        'voc'
    ];

    public function getStations(): array
    {
        $result = [];
        foreach ($this->child_sources as $source) {
            $url = $this->base_url . "/v1/sensors";
            $response = Http::withHeaders([
                "X-API-KEY" => $this->auth_secret
            ])
            ->get($url, [
                'fields'    => implode(',', self::STATION_FIELDS),
                'location_type' => 0
            ]);
            $json_response = $response->json();
            $fields = $response['fields'];
            $fields_mapping = self::FIELD_MAPPING;
            $fields = array_map(function ($value) use ($fields_mapping) {
                return $fields_mapping[$value] ?? $value;
            }, $fields);

            $items = $json_response['data'];

            foreach ($items as $item) {
                $item = array_combine($fields, $item);
                if (!is_numeric($item['latitude'])) {
                    $item['latitude'] = 0;
                }
                if (!is_numeric($item['longitude'])) {
                    $item['longitude'] = 0;
                }
                $item['installed_date'] = Carbon::createFromTimestamp($item['installed_date']);
                $station_vo = new StationVO(
                    name: $item['name'],
                    address: '',
                    source: $source,
                    source_id: strval($item['source_id']),
                    latitude: floatval($item['latitude']),
                    longitude: floatval($item['longitude']),
                    altitude: floatval($item['altitude']),
                    photo_url: null,
                    installed_at: $item['installed_date'],
                );

                $result[] = $station_vo;
            }
        }

        return $result;
    }

    public function getDataInTimeRangeOneStation(Station $station, Carbon $start_time, Carbon $end_time): array
    {
        throw new Exception('No data for PurpleAir');
    }

    public function getLatestDataOneStation(Station $station): array
    {
        $result = [];
        $response = Http::withHeaders([
            "X-API-KEY" => $this->auth_secret
        ])->get($this->base_url . "/v1/sensors", [
            'fields' => implode(',', self::DATA_FIELDS),
            'location_type' => 0
        ]);

        $response = $response->json();
        $fields = $response['fields'];
        $items = $response['data'];
        foreach ($items as $item) {
            $item = array_combine($fields, $item);
            $item = array_filter($item, 'strlen');
            if (count($item) <= 2) { // don't have any value except sensor_index and last_seen
                continue;
            }

            $station = $this->station_repository_interface->findOneBy(['source_id' => $item['sensor_index'], 'source' => $this->config_name]);
            if (!$station) {
                continue;
            }

            $result[] = $this->transformOneDataToVO($station, $item);
        }
        return $result;
    }

    public function transformOneDataToVO(Station $station, array $data): StationDataVO
    {
        $daqi_service = AQIService::getAQIService(AQIDictionary::DAQI);
        $caqi_service = AQIService::getAQIService(AQIDictionary::CAQI);
        $usaqi_service = AQIService::getAQIService(AQIDictionary::USAQI);
        $unit_conversion_service = new UnitConversionService();

        $unix_timestamp = $data['last_seen'];
        $timestamp = Carbon::createFromTimestamp($unix_timestamp);

        $daqi = null;
        $daqi_factor = '';
        $caqi = null;
        $caqi_factor = '';
        $usaqi = null;
        $usaqi_factor = '';
        $sensor_items = [];

        $temperature = $data['temperature'] ?? SensorTypeDictionary::DEFAULT_TEMPERATURE;
        $pressure = $data['pressure'] ?? SensorTypeDictionary::DEFAULT_PRESSURE;

        $collected_data = [
            SensorTypeDictionary::TEMPERATURE => [
                'value' => $unit_conversion_service->convert($temperature, UnitDictionary::UNIT_FAHRENHEIT, UnitDictionary::UNIT_CELSIUS),
                'unit' => UnitDictionary::UNIT_CELSIUS,
            ],
            SensorTypeDictionary::PRESSURE => [
                'value' => $pressure,
                'unit' => UnitDictionary::UNIT_HECTOPASCAL,
            ],
            SensorTypeDictionary::HUMIDITY => [
                'value' => $data['humidity'] ?? null,
                'unit' => UnitDictionary::UNIT_HUMIDITY,
            ],
            SensorTypeDictionary::PM1 => [
                'value' => $data['pm1.0'] ?? null,
                'unit' => UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
            ],
            SensorTypeDictionary::PM2_5 => [
                'value' => $data['pm2.5'] ?? null,
                'unit' => UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
            ],
            SensorTypeDictionary::PM10 => [
                'value' => $data['pm10.0'] ?? null,
                'unit' => UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
            ],
            SensorTypeDictionary::VOC => [
                'value' => $data['voc'] ?? null,
                'unit' => UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
            ],
            SensorTypeDictionary::O3 => [
                'value' => $data['ozone1'] ?? null,
                'unit' => UnitDictionary::UNIT_PARTS_PER_BILLION,
            ],
        ];

        foreach ($collected_data as $sensor_type_name => $value) {
            if (!$value['value']){
                continue;
            }
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

    public function canSaveLatestDataToDatabase(): bool
    {
        return true;
    }
}
