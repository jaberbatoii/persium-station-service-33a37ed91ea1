<?php

namespace Persium\Station\Domain\CommandBus\GetLatestDataOneStation;

use Persium\Station\Domain\Entities\Station\Station;

class GetLatestDataOneStationCommand
{
    public function __construct(
       public Station $station
    )
    {
    }
}
