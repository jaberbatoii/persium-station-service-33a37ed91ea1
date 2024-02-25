<?php

declare(strict_types=1);

namespace Persium\Station\Domain\CommandBus\GetWindMap;

use Persium\Station\Domain\Entities\Wind\WindPointRepositoryInterface;
use Persium\Station\Domain\Services\Helper\MapHelper;

class GetWindMapHandler
{
    public function __construct(
        private readonly WindPointRepositoryInterface $wind_point_repository,
        private readonly MapHelper $map_helper,
    )
    {
    }

    public function handle(
        GetWindMapCommand $command,
    ) : ?array
    {
        $ref_time = null;
        $wind_h_values = [];
        $wind_v_values = [];
        $lat_max = -180;
        $lat_min = -180;
        $lng_max = -180;
        $lng_min = -180;

        $wind_data = $this->wind_point_repository->getAllWithData();
        foreach ($wind_data as $wind_datum) {
            $lat = $wind_datum->getLatitude();
            $lng = $wind_datum->getLongitude();
            if ($lat > $lat_max) {
                $lat_max = $lat;
            }
            if ($lat < $lat_min) {
                $lat_min = $lat;
            }
            if ($lng > $lng_max) {
                $lng_max = $lng;
            }
            if ($lng < $lng_min) {
                $lng_min = $lng;
            }
            /** var WindPoint $wind_datum */
            $data = $wind_datum->getData()->first();
            if ($data) {
                $wind_h_values[] = [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'value' => -($data->getSpeed() * sin($data->getDirection() * M_PI / 180)),
                ];

                $wind_v_values[] = [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'value' => -($data->getSpeed() * cos($data->getDirection() * M_PI / 180)),
                ];

                $ref_time = $data->getTimestamp();
            }
        }

        $h_wind_map = $this->generateMap(
            $lat_max, $lat_min,
            $lng_max, $lng_min,
            $command->x_dimension, $command->y_dimension,
            $wind_h_values
        );
        $v_wind_map = $this->generateMap(
            $lat_max, $lat_min,
            $lng_max, $lng_min,
            $command->x_dimension, $command->y_dimension,
            $wind_v_values
        );

        return [
            [
                'header' => [
                    'parameterUnit' => 'm.s-1',
                    'parameterNumber' => 2,
                    'parameterNumberName' => 'eastward_wind',
                    'parameterCategory' => 2,
                    'refTime' => $ref_time->format('Y-m-d H:i:s'),
                    'dx' => 0.77,
                    'dy' => 0.5,
                    'nx' => 17,
                    'ny' => 17,
                    // top left and bottom right
                    'la1' => $lat_max,
                    'la2' => $lat_min,
                    'lo1' => $lng_min,
                    'lo2' => $lng_max,
                ],
                'data'   => $h_wind_map
            ],
            [
                'header' => [
                    'parameterUnit' => 'm.s-1',
                    'parameterNumber' => 3,
                    'parameterNumberName' => 'northward_wind',
                    'parameterCategory' => 2,
                    'refTime' => $ref_time->format('Y-m-d H:i:s'),
                    'dx' => 0.77,
                    'dy' => 0.5,
                    'nx' => 17,
                    'ny' => 17,
                    // top left and bottom right
                    'la1' => $lat_max,
                    'la2' => $lat_min,
                    'lo1' => $lng_min,
                    'lo2' => $lng_max,
                ],
                'data'   => $v_wind_map
            ]
        ];
    }

    private function generateMap(
        float $lat_max, float $lat_min, float $lng_max, float $lng_min,
        int $x_di, int $y_di, array $values
    ): array
    {
        // Divine 4 corner to x_cords and y_cords according to $x_di, $y_di (ex: 16: 9)
        $x_unit = ($lng_max - $lng_min)/$x_di;
        $y_unit = ($lat_max - $lat_min)/$y_di;

        // lon go from left to right of the grid
        $x_cords = [];
        for ($i = 0; $i <= $x_di; $i++) {
            $x_cords[] = $lng_min + $i * $x_unit;
        }

        // lat go from top to bottom of the grid
        $y_cords = [];
        for ($i = 0; $i <= $y_di; $i++) {
            $y_cords[] = $lat_max - $i * $y_unit;
        }

        $data = [];
        for ($i = 0; $i < count($y_cords) - 1; $i++) {
            for ($j = 0; $j < count($x_cords) - 1; $j++) {
                $lat = $y_cords[$i];
                $lng = $x_cords[$j];
                $data[] = $this->getNearestPointData($values, $lat, $lng);
            }
        }

        return $data;
    }

    private function getNearestPointData($arr_data, $lat, $lon)
    {
        $min_distance = 10000;
        $value = 0;
        foreach ($arr_data as $item) {
            $compare_lat = $item['latitude'];
            $compare_lon = $item['longitude'];
            if ($this->map_helper->distance($lat, $lon, $compare_lat, $compare_lon, 'K') < $min_distance) {
                $min_distance = $this->map_helper->distance($lat, $lon, $compare_lat, $compare_lon, 'K');
                $value = $item['value'];
            }
        }
        return $value;
    }
}
