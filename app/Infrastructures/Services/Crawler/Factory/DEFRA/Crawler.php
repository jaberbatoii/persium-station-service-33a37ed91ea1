<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler\Factory\DEFRA;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
use Persium\Station\Infrastructures\Persistency\Redis\Repositories\DEFRARepository;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerBase;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;

class Crawler extends CrawlerBase implements CrawlerInterface
{
    protected string $config_name = 'DEFRA';
    public function getStations(): array
    {
        $result = [];
        //Ignore duplicate source_id
        $collected_source_ids = [];
        foreach ($this->child_sources as $source) {
            $url = $this->base_url . "/GetImportMeta?Source=%22{$source}%22&GetOnlyLocationVars=TRUE";
            $response = Http::get($url);
            $items = $response->json();
            $items = json_decode($items[0]);

            foreach ($items as $item) {
                if (in_array($item->code, $collected_source_ids)) {
                    continue;
                }
                $station_vo = new StationVO(
                    name: $item->site ?? $item->code,
                    address: '',
                    source: $source,
                    source_id: $item->code,
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    altitude: 0,
                    photo_url: null,
                    installed_at: null,
                );

                $collected_source_ids[] = $item->code;

                $result[] = $station_vo;
            }
        }

        return $result;
    }

    public function getDataInTimeRangeOneStation(Station $station, Carbon $start_time, Carbon $end_time): array
    {
        $result = [];
        $start_date = $start_time->format('Y-m-d');
        $end_date = $end_time->format('Y-m-d');
        $start_time = $start_time->format('H');
        $end_time = $end_time->format('H');

        $url = $this->base_url . "/GetDefraDS?Source=%22{$station->getSource()}%22&Stations=[%22{$station->getSourceID()}%22]&Pollutants=[%22all%22]&DatetimeFrom=%22{$start_date}%20{$start_time}%22&DatetimeTo=%22{$end_date}%20{$end_time}%22"; //phpcs:ignore;
        $response = Http::timeout(300)->get($url);
        $items = $response->json();
        $items = json_decode($items[0]);
        foreach ($items as $item) {
            $item = json_decode($item);
            if ($item->code == $station->getSourceID()) {
                $result[] = $this->transformOneDataToVO($station, $item);
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

        $ignored_properties = ['code', 'site', 'latitude', 'longitude', 'pollutant', 'date', 'site_type'];
        $sensor_data_vos = [];
        $daqi = null;
        $daqi_factor = '';
        $caqi = null;
        $caqi_factor = '';
        $usaqi = null;
        $usaqi_factor = '';

        $temperature = SensorTypeDictionary::DEFAULT_TEMPERATURE;
        $pressure = SensorTypeDictionary::DEFAULT_PRESSURE;

        $timestamp = Carbon::parse($data['date']);
        $collected_data = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored_properties) && is_float($value)) {
                // CO is sending in mg/m3
                if ($key == SensorTypeDictionary::CO){
                    $value = 1000 * $value;
                }
                $sensor_name = $sensor_type_dictionary_service->convertRawNameToName($key);
                if ($sensor_name == SensorTypeDictionary::TEMPERATURE) {
                    $temperature = $value;
                }

                if ($sensor_name == SensorTypeDictionary::PRESSURE) {
                    $pressure = $value;
                }

                $collected_data[$sensor_name] = [
                    'value' => $value,
                    'unit' => UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
                ];
            }
        }

        foreach ($collected_data as $sensor_name => $value) {
            $value = $value['value'];
            $ppb = null;
            $ugm3 = $value;
            $sensor_daqi = null;
            $sensor_caqi = null;
            $sensor_usaqi = null;
            $mole_weight = SensorTypeDictionary::MOLE_WEIGHTS[strtoupper($sensor_name)] ?? null;
            if ($mole_weight){
                $ppb = $unit_conversion_service->ugm3ToPpb($value, $mole_weight, $temperature, $pressure);
            }

            if ($ugm3){
                $sensor_daqi = $daqi_service->calculateAQI(
                    $sensor_name,
                    $ugm3
                );
                if ($sensor_daqi > $daqi) {
                    $daqi = $sensor_daqi;
                    $daqi_factor = $sensor_name;
                }

                $caqi_temp = $caqi_service->calculateAQI(
                    $sensor_name,
                    $ugm3
                );
                if ($caqi_temp > $caqi) {
                    $caqi = $caqi_temp;
                    $caqi_factor = $sensor_name;
                }
            }

            if ($ppb){
                $sensor_usaqi = $usaqi_service->calculateAQI(
                    $sensor_name,
                    $ppb
                );
                if ($sensor_usaqi > $usaqi) {
                    $usaqi = $sensor_usaqi;
                    $usaqi_factor = $sensor_name;
                }
            }

            $sensor_data_vos[] = new StationSensorDataVO(
                timestamp: $timestamp,
                name: $sensor_name,
                value: $value,
                ugm3: $ugm3,
                ppb: $ppb,
                unit: UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
                daqi: $sensor_daqi,
                caqi: $sensor_caqi,
                usaqi: $sensor_usaqi,
            );
        }

        return new StationDataVO(
            station: $station,
            timestamp: $timestamp,
            sensor_data_vos: $sensor_data_vos,
            caqi: $caqi,
            caqi_factor: $caqi_factor,
            usaqi: $usaqi,
            usaqi_factor: $usaqi_factor,
            daqi: $daqi,
            daqi_factor: $daqi_factor,
        );
    }

    public function prepareStationsToGetLatestData(): array
    {
        $result = [];
        $collected_stations = [];

        $defra_repository = new DEFRARepository();

        $pollutants = [
            SensorTypeDictionary::SO2 => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/1',
            SensorTypeDictionary::NO2 => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/8',
            SensorTypeDictionary::O3 => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/7',
            SensorTypeDictionary::PM10 => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/5',
            SensorTypeDictionary::PM2_5 => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/6001',
            SensorTypeDictionary::CO => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/10',
            SensorTypeDictionary::NO => 'http://dd.eionet.europa.eu/vocabulary/aq/pollutant/38',
        ];

        //Combine station with identifier
        foreach ($pollutants as $name => $url) {
            $sensors = $this->getSensors($url);
            foreach ($sensors as $sensor) {
                if (!isset($collected_stations[$sensor['name']])){
                    $collected_stations[$sensor['name']] = [
                        'latitude'  => $sensor['latitude'],
                        'longitude' => $sensor['longitude'],
                        'pollutants' => [],
                    ];
                }
                $collected_stations[$sensor['name']]['pollutants'][$name] = [
                    'url' => $url,
                    'identifier' => $sensor['identifier'],
                ];
            }
        }

        foreach ($collected_stations as $name => $value) {
            $station = $this->station_repository_interface->findNearestOneByLatAndLngInDistanceBySource(
                $value['latitude'],
                $value['longitude'],
                10,
                $this->child_sources
            );
            if (!$station){
                $station = new Station(
                    name: $name,
                    address: '',
                    source: 'DEFRA',
                    source_id: Str::uuid()->toString(),
                    latitude: $value['latitude'],
                    longitude: $value['longitude'],
                    altitude: 0,
                    photo_url: '',
                    installed_at: now(),
                );

                $this->station_repository_interface->save($station);
            }

            $defra_repository->setLatestDataConfig($station->getId(), $value['pollutants']);

            $result[] = $station;
        }

        return $result;
    }

    private function getSensors($pollutant_url): array
    {
        $result = [];
        $url = 'https://uk-air.defra.gov.uk/sos-ukair/service/json';

        $response = Http::post($url, [
            'service' => 'SOS',
            'version' => '2.0.0',
            'request' => 'GetFeatureOfInterest',
            'observedProperty' => $pollutant_url
        ]);

        $response = $response->json();
        if (!$response || !isset($response['featureOfInterest'])){
            return [];
        }

        foreach ($response['featureOfInterest'] as $item) {
            if (!is_array($item)){
                continue;
            }
            $name = explode("-", $item['name'])[0];
            if ($name == 'GB_SamplingFeature_missingFOI'){
                continue;
            }
            $result[] = [
                'name' => explode("-", $item['name'])[0],
                'latitude'  => $item['geometry']['coordinates'][0],
                'longitude' => $item['geometry']['coordinates'][1],
                'identifier' => $item['identifier']
            ];
        }

        return $result;
    }

    public function getLatestDataOneStation(Station $station): array
    {
        $result = [];
        $timeframes = [];
        $start_time = Carbon::now()->subDays(3);
        $end_time = Carbon::now()->addHours(2);
        $start_time_format =  "{$start_time->format('Y-m-d')}T00:00:00+01:00";
        $end_time_format = "{$end_time->format('Y-m-d')}T23:59:00+01:00";

        $defra_repository = new DEFRARepository();
        $pollutants = $defra_repository->getLatestDataConfig($station->getId());
        foreach ($pollutants as $name => $value) {
            $data = [
                'service' => 'SOS',
                'version' => '2.0.0',
                'request' => 'GetObservation',
                'observedProperty' => $value['url'],
                'featureOfInterest' => $value['identifier'],
                'temporalFilter' => [
                    'during' => [
                        'ref' => 'om:phenomenonTime',
                        'value' => [
                            $start_time_format,
                            $end_time_format
                        ]
                    ]
                ]
            ];
            try {
                $response = Http::timeout(60)->post('https://uk-air.defra.gov.uk/sos-ukair/service/json', $data);
            } catch (\Exception $e) {
                continue;
            }
            $response = $response->json();
            if (!is_array($response) || !isset($response['observations']) || !is_array($response['observations'])){
                continue;
            }
            foreach ($response['observations'] as $item) {
                $timestamp = Carbon::parse($item['phenomenonTime'][1]);
                $unix_timestamp = $timestamp->getTimestamp();
                $value = $item['result']['value'];

                if ($value <= 0){
                    continue;
                }

                if (!isset($timeframes[$unix_timestamp])){
                    $timeframes[$unix_timestamp] = [];
                }

                $timeframes[$unix_timestamp][$name] = $value;
            }
        }

        foreach ($timeframes as $unix_timestamp => $data) {
            $transform_data = [
                'date' => Carbon::createFromTimestamp($unix_timestamp)->toDateTimeString()
            ];
            foreach ($data as $pollutant_name => $value) {
                $transform_data[$pollutant_name] = $value;
            }
            $result[] = $this->transformOneDataToVO($station, $transform_data);
        }

        return $result;
    }

    public function canSaveLatestDataToDatabase(): bool
    {
        return false;
    }
}
