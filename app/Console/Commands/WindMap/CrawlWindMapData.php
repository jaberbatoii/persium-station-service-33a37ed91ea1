<?php

declare(strict_types = 1);

namespace Persium\Station\Console\Commands\WindMap;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Persium\Station\Domain\Entities\Wind\WindData;
use Persium\Station\Domain\Entities\Wind\WindDataRepositoryInterface;
use Persium\Station\Domain\Entities\Wind\WindPointRepositoryInterface;

class CrawlWindMapData extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "crawl:wind-map-data";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl:wind-map-data";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(
        WindPointRepositoryInterface $wind_point_repository,
        WindDataRepositoryInterface $wind_data_repository,
    ): int {
        $this->info('Start crawl wind map data');
        $wind_point_repository->deleteAllWindData();
        $points = $wind_point_repository->findAll();
        foreach ($points as $point) {
            /** var WindPoint $point */
            // limit request weather api is 60 request per minute
            usleep(1001);
            $response = Http::get(env('OPEN_WEATHER_MAP_API_URL'), [
                'lat' => $point->getLatitude(),
                'lon' => $point->getLongitude(),
                'appid' => env('OPEN_WEATHER_MAP_API_KEY'),
            ]);

            $data = $response->json();

            if (!isset($data['wind'])) {
                $this->error("Error: no wind data for point {$point->getId()}");
            }

            $wind_speed = $data['wind']['speed'];
            $wind_direction = $data['wind']['deg'];
            $unix_timestamp = $data['dt'];
            $server_timezone = 'UTC';
            $timestamp = Carbon::createFromTimestamp($unix_timestamp)->setTimezone($server_timezone);
            $wind_data = new WindData(
                wind_point: $point,
                timestamp: $timestamp,
                speed: $wind_speed,
                direction: $wind_direction,
            );

            $point->addData($wind_data);

            $wind_point_repository->save($point);

            $this->info("Success: wind data for point {$point->getId()}");
        }
        $this->info('End crawl wind map data');
        return 0;
    }
}
