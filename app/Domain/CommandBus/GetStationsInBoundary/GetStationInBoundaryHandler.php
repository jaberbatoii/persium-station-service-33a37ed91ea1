<?php

namespace Persium\Station\Domain\CommandBus\GetStationsInBoundary;

use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;

class GetStationInBoundaryHandler
{
    public function __construct(
        private readonly StationRepositoryInterface $station_repository_interface
    )
    {
    }

    public function handle(
        GetStationInBoundaryCommand $command,
    ) : ?array
    {
        $lat_max = $command->lat_max;
        $lat_min = $command->lat_min;
        $lng_max = $command->lng_max;
        $lng_min = $command->lng_min;

        return $this->station_repository_interface->findInBoundWithStatus(
            $lat_max, $lat_min, $lng_max, $lng_min,
            [Station::STATUS_ONLINE, Station::STATUS_OFFLINE]);
    }
}
