<?php

namespace Tests\Feature\Route;

use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Tests\RefreshDatabase;
use Tests\TestCase;

class StationControllerTest extends TestCase
{
    use RefreshDatabase;

    private StationRepositoryInterface $station_repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->station_repository = app(StationRepositoryInterface::class);
        $this->seed();
    }

    public function testGetStationsInBoundarySuccess()
    {
        $response = $this->get('/api/station/in-boundary?lat1=0&lat2=2&lng1=0&lng2=2&lat3=0&lat4=2&lng3=0&lng4=2');

        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            '*' => [
                'uuid',
                'latitude',
                'longitude',
                'aqi',
            ],
        ]);
    }

    private function seed()
    {
        $station = new Station(
            name: 'name1',
            address: 'address',
            source: 'source1',
            source_id: 'source_id1',
            latitude: 1.0,
            longitude: 1.0,
            altitude: 1.0,
            photo_url: null,
            installed_at: now(),
            status: Station::STATUS_ONLINE,
        );

        $this->station_repository->save($station);
    }
}
