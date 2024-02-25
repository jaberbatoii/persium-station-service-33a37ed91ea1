<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler\Factory\CEM;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Persium\Station\Dictionary\UnitDictionary;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\ValueObjects\StationDataVO;
use Persium\Station\Domain\ValueObjects\StationSensorDataVO;
use Persium\Station\Domain\ValueObjects\StationVO;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerBase;
use Persium\Station\Infrastructures\Services\Crawler\Factory\CrawlerInterface;

class Crawler extends CrawlerBase implements CrawlerInterface
{
    protected string $config_name = 'CEM';
    public function getStations(): array
    {
        $result = [];
        foreach ($this->child_sources as $source) {
            $url = $this->base_url . "/eip/default/call/json/get_station_by_conditions";
            $response = Http::get($url);
            $body = $response->json();
            $body = $body['html'];
            $records = $this->getBetween($body, '<option ', '</option>');
            array_shift($records);
            foreach ($records as $record) {
                $source_id = $this->getBetween($record, 'data-id="', '"')[1];
                $latitude = $this->getBetween($record, 'data-lat="', '"')[1];
                $longitude = $this->getBetween($record, 'data-lon="', '"')[1];
                $address = $this->getBetween($record, 'data-address="', '"')[1];
                $name = substr($record, strpos($record, ">") + 1);
                $station_vo = new StationVO(
                    name: $name,
                    address: $address,
                    source: $source,
                    source_id: $source_id,
                    latitude: floatval($latitude),
                    longitude: floatval($longitude),
                    altitude: 0,
                    photo_url: null,
                    installed_at: null,
                );
                $result[] = $station_vo;
            }
        }

        return $result;
    }

    public function getDataInTimeRangeOneStation(Station $station, Carbon $start_time, Carbon $end_time): array
    {
        # No data for CEM
        throw new Exception('No data for CEM');
    }

    public function getLatestDataOneStation(Station $station): array
    {
        $timeframes = [];
        $result = [];
        $source_id = $station->getSourceID();
        $url = 'http://enviinfo.cem.gov.vn/eos/services/call/json/qi_detail_for_eip';
        $response = Http::post($url, [
            'station_id' => $source_id,
        ]);

        $response = $response->json();
        if (!$response || !isset($response['res']) || !is_array($response['res'])) {
            return [];
        }

        foreach ($response['res'] as $name => $data) {
            $values = $data['values'];
            foreach ($values as $value) {
                foreach ($value as $usaqi => $str_time) {
                    // Data returned as local time
                    $time = Carbon::createFromTimeString($str_time, 'Asia/Ho_Chi_Minh');
                    if (!isset($timeframes[$time->unix()])) {
                        $timeframes[$time->unix()] = [];
                    }
                    $timeframes[$time->unix()][$name] = $usaqi;
                }
            }
        }
        foreach ($timeframes as $unix_timestamp => $timeframe) {
            $timeframe['unix_time'] = $unix_timestamp;
            $result[] = $this->transformOneDataToVO($station, $timeframe);
        }
        return $result;
    }

    private function getBetween($content, $start, $end): array
    {
        $n = explode($start, $content);
        $result = array();
        foreach ($n as $val) {
            $pos = strpos($val, $end);
            if ($pos !== false) {
                $result[] = substr($val, 0, $pos);
            }
        }
        return $result;
    }

    public function transformOneDataToVO(Station $station, array $data): StationDataVO
    {
        $unix_timestamp = $data['unix_time'];
        unset($data['unix_time']);
        $sensor_data_vos = [];
        $usaqi = null;
        $usaqi_factor = '';
        foreach ($data as $name => $pollutant_usaqi) {
            $sensor_data_vos[] = new StationSensorDataVO(
                timestamp: Carbon::createFromTimestamp($unix_timestamp),
                name: $name,
                value: null,
                ugm3: null,
                ppb: null,
                unit: UnitDictionary::UNIT_MICROGRAM_PER_CUBIC_METER,
                daqi: null,
                caqi: null,
                usaqi: $usaqi,
            );

            if ($pollutant_usaqi > $usaqi) {
                $usaqi = $pollutant_usaqi;
                $usaqi_factor = $name;
            }
        }

        return new StationDataVO(
            station: $station,
            timestamp: Carbon::createFromTimestamp($unix_timestamp),
            sensor_data_vos: $sensor_data_vos,
            caqi: null,
            caqi_factor: '',
            usaqi: $usaqi,
            usaqi_factor: $usaqi_factor,
            daqi: null,
            daqi_factor: '',
        );
    }

    public function canSaveLatestDataToDatabase(): bool
    {
        return true;
    }
}
