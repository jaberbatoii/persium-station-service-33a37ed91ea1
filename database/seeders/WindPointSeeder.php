<?php

declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Persium\Station\Domain\Entities\Wind\WindPoint;
use Persium\Station\Domain\Entities\Wind\WindPointRepositoryInterface;

class WindPointSeeder extends Seeder
{
    public function run(
        WindPointRepositoryInterface $wind_point_repository
    ): void
    {
        echo('Start seeding wind_speed point table from CSV file....');
        $file_handle = fopen(storage_path('app/data/seeders/windgrid.csv'), 'r');
        $is_header = true;
        $n = 0;
        while (!feof($file_handle)) {
            $line = fgetcsv($file_handle, 0, ',');

            if (empty($line) || empty($line[0]) || empty($line[1])) {
                break;
            }

            if ($is_header) {
                $is_header = false;
            } else {
                $wind_point = new WindPoint(
                    latitude: floatval($line[0]),
                    longitude: floatval($line[1]),
                );
                $wind_point_repository->save($wind_point);
            }

            echo('Seeded ' . ++$n . ' wind_speed point');
        }

        echo('End seeding wind_speed point table from CSV file....');
    }
}
