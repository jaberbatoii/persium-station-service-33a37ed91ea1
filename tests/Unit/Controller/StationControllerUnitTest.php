<?php

namespace Tests\Unit\Controller;

use Joselfonseca\LaravelTactician\Bus;
use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Services\Cache\StationDataCacheInterface;
use Persium\Station\Domain\ValueObjects\StationAQIDataVO;
use Persium\Station\Http\Controllers\StationController;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;

class StationControllerUnitTest extends TestCase
{
    public function testGetInBoundarySuccess()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'lat1' => 1,
            'lat2' => 1,
            'lng1' => 1,
            'lng2' => 1,
            'lat3' => 1,
            'lat4' => 1,
            'lng3' => 1,
            'lng4' => 1,
        ]);
        //mock bus
        $bus = $this->getMockBuilder(Bus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $station_data_cache = $this->getMockBuilder(StationDataCacheInterface::class)->getMock();
        $station_repository = $this->getMockBuilder(StationRepositoryInterface::class)->getMock();
        $bus->method('addHandler')->willReturn(true);
        $station = $this->createMock(Station::class);
        $station->method('getID')->willReturn(1);
        $station->method('isOnline')->willReturn(true);
        $station->method('getLatitude')->willReturn(1.1);
        $station->method('getLongitude')->willReturn(1.1);
        $station->method('getUUID')->willReturn('test-uuid');
        $bus->method('dispatch')->willReturn([$station]);
        $station_data_vo = new StationAQIDataVO(
            value: 1,
            type: 1,
            factor: 'PM2.5',
            timestamp: now()
        );
        $station_data_cache->method('getLatestAQIData')->willReturn($station_data_vo);

        $response = (new StationController($bus, $station_data_cache, $station_repository))
            ->getInBoundary($request);
        $this->assertEquals(200, $response->getStatusCode());
        $expected_result = [
            'data' => [[
                'uuid' => 'test-uuid',
                'latitude' => 1.1,
                'longitude' => 1.1,
                'aqi' => 1,
            ]]
        ];
        $this->assertEquals( json_encode($expected_result), $response->getContent());
    }

    public function testGetInBoundaryFailWrongCoordinates()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'lat1' => 1,
            'lat2' => 2,
            'lng1' => 3,
            'lng2' => 4,
            'lat3' => 5,
            'lat4' => 6,
            'lng3' => 7,
            'lng4' => 1,
        ]);
        //mock bus
        $bus = $this->getMockBuilder(Bus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $station_data_cache = $this->getMockBuilder(StationDataCacheInterface::class)->getMock();
        $station_repository = $this->getMockBuilder(StationRepositoryInterface::class)->getMock();
        $bus->method('addHandler')->willReturn(true);

        $response = (new StationController($bus, $station_data_cache, $station_repository))
            ->getInBoundary($request);
        $this->assertEquals(400, $response->getStatusCode());
        $expected_result = [
            'error' => 'The four points do not form a rectangle.',
        ];
        $this->assertEquals( json_encode($expected_result), $response->getContent());
    }

    public function testShowSuccess()
    {
        //mock bus
        $bus = $this->getMockBuilder(Bus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $station = $this->createMock(Station::class);
        $station->method('getID')->willReturn(1);
        $station->method('getName')->willReturn('name');
        $station->method('isOnline')->willReturn(true);
        $station->method('getLatitude')->willReturn(1.1);
        $station->method('getLongitude')->willReturn(1.1);
        $station->method('getUUID')->willReturn('test-uuid');
        $station->method('getInstalledAt')->willReturn(now());
        $station->method('getAddress')->willReturn('address');
        $station->method('getSource')->willReturn('source');
        $station->method('getSourceID')->willReturn('source-id');
        $station->method('getPhotoURL')->willReturn('photo-url');
        $station_data_cache = $this->getMockBuilder(StationDataCacheInterface::class)->getMock();
        $station_repository = $this->getMockBuilder(StationRepositoryInterface::class)->getMock();
        $station_repository->method('findOneByUuid')->willReturn($station);
        $bus->method('addHandler')->willReturn(true);
        $station_data_vo = new StationAQIDataVO(
            value: 1,
            type: 1,
            factor: 'PM2.5',
            timestamp: now()
        );
        $station_data_cache->method('getLatestAQIData')->willReturn($station_data_vo);
        $response = (new StationController($bus, $station_data_cache, $station_repository))
            ->show('test-uuid');
        $this->assertEquals(200, $response->getStatusCode());
        $expected_result = [
            'data' => [
                'uuid' => 'test-uuid',
                'name'  => 'name',
                'address' => 'address',
                'latitude' => 1.1,
                'longitude' => 1.1,
                'source' => 'source',
                'source_id' => 'source-id',
                'photo_url' => 'photo-url',
                'installed_at' => now()->toDateTimeString(),
            ]
        ];

        $this->assertEquals( json_encode($expected_result), $response->getContent());
    }

    public function testShowNotFound()
    {
        //mock bus
        $bus = $this->getMockBuilder(Bus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $station_data_cache = $this->getMockBuilder(StationDataCacheInterface::class)->getMock();
        $station_repository = $this->getMockBuilder(StationRepositoryInterface::class)->getMock();
        $station_repository->method('findOneByUuid')->willReturn(null);

        $response = (new StationController($bus, $station_data_cache, $station_repository))
            ->show('test-uuid');
        $bus->method('addHandler')->willReturn(true);
        $this->assertEquals(404, $response->getStatusCode());
        $expected_result = [
            'error' => [
                'message' => 'Station not found.',
            ]
        ];

        $this->assertEquals( json_encode($expected_result), $response->getContent());
    }
}
