<?php

namespace Persium\Station\Domain\CommandBus\GetStationsInBoundary;

use Persium\Station\Domain\DTOs\Request\StationInBoundaryRequest;
use Persium\Station\Domain\Entities\Station\Station;

class GetStationInBoundaryCommand
{
    public float $lat_max;
    public float $lat_min;
    public float $lng_max;
    public float $lng_min;
    public function __construct(
        StationInBoundaryRequest $request,
        public array $statuses = [Station::STATUS_ONLINE, Station::STATUS_OFFLINE]
    )
    {
        $this->lat_max = max($request->lat1, $request->lat2, $request->lat3, $request->lat4);
        $this->lat_min = min($request->lat1, $request->lat2, $request->lat3, $request->lat4);
        $this->lng_max = max($request->lng1, $request->lng2, $request->lng3, $request->lng4);
        $this->lng_min = min($request->lng1, $request->lng2, $request->lng3, $request->lng4);
    }
}
