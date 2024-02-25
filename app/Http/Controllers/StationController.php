<?php

declare(strict_types = 1);

namespace Persium\Station\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Joselfonseca\LaravelTactician\CommandBusInterface;
use Laravel\Lumen\Routing\Controller as BaseController;
use Persium\Station\Domain\CommandBus\GetStationsInBoundary\GetStationInBoundaryCommand;
use Persium\Station\Domain\DTOs\Request\StationInBoundaryRequest;
use Persium\Station\Domain\DTOs\Response\StationDTO;
use Persium\Station\Domain\DTOs\Response\StationInBoundaryResponseDTO;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Psr\Http\Message\ServerRequestInterface;

class StationController extends BaseController
{

    public function __construct(
        private readonly CommandBusInterface $bus,
        private readonly StationDataCacheInterface $station_data_cache,
        private readonly StationRepositoryInterface $station_repository,
    )
    {
    }

    public function getInBoundary(
        ServerRequestInterface           $request,
    ): JsonResponse
    {
        $result = [];

        try {
            $request_data = StationInBoundaryRequest::createFromRequest($request);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
        $aqi_type = $request_data->aqi_type;

        $stations = $this->bus->dispatch(new GetStationInBoundaryCommand($request_data));
        foreach ($stations as $station) {
            if (!($station instanceof Station)){
                throw new \Exception('station is not instance of Station');
            }

            $aqi = -1;
            if ($station->isOnline() && $latest_data = $this->station_data_cache->getLatestAQIData($station->getID(), $aqi_type)) {
                $aqi = $latest_data->getValue();
            }
            $result[] = new StationInBoundaryResponseDTO(
                uuid: $station->getUUID(),
                latitude: $station->getLatitude(),
                longitude: $station->getLongitude(),
                aqi: $aqi,
            );
        }

        return response()->json([
            'data' => $result
        ]);
    }

    public function show(string $uuid): JsonResponse
    {
        $station = $this->station_repository->findOneByUuid($uuid);
        if (!$station) {
            return response()->json([
                'error' => [
                    'message' => 'Station not found.'
                ]
            ], 404);
        }

        return response()->json([
            'data' => StationDTO::createFromEntity($station)
        ]);
    }
}
