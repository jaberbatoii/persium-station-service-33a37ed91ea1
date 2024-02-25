<?php

declare(strict_types = 1);

namespace Tests\Unit\Entities\Station;

use Persium\Station\Domain\Entities\Station\Station;
use PHPUnit\Framework\TestCase;

class StationTest extends TestCase
{
    public function testConstruct()
    {
        $station = new Station(
            name: 'name',
            address: 'address',
            source: 'source',
            source_id: 'source_id',
            latitude: 1.0,
            longitude: 1.0,
            altitude: 1.0,
            photo_url: 'photo_url',
            installed_at: null,
            status: 1
        );

        $this->assertNotNull($station->getUUID());
        $this->assertNotNull($station->getCreatedAt());
        $this->assertNotNull($station->getUpdatedAt());
        $this->assertNull($station->getDeletedAt());

        $this->assertEquals('name', $station->getName());
        $this->assertEquals('address', $station->getAddress());
        $this->assertEquals('source', $station->getSource());
        $this->assertEquals('source_id', $station->getSourceId());
        $this->assertEquals(1.0, $station->getLatitude());
        $this->assertEquals(1.0, $station->getLongitude());
        $this->assertEquals(1.0, $station->getAltitude());
        $this->assertEquals('photo_url', $station->getPhotoUrl());
        $this->assertEquals(1, $station->getStatus());
    }

    public function testSet()
    {
        $station = new Station(
            name: 'name',
            address: 'address',
            source: 'source',
            source_id: 'source_id',
            latitude: 1.0,
            longitude: 1.0,
            altitude: 1.0,
            photo_url: 'photo_url',
            installed_at: null,
            status: 1
        );

        $station->setName('new_name');
        $station->setAddress('new_address');
        $station->setSource('new_source');
        $station->setSourceId('new_source_id');
        $station->setLatitude(2.0);
        $station->setLongitude(2.0);
        $station->setAltitude(2.0);
        $station->setPhotoURL('new_photo_url');
        $station->setStatus(2);

        $this->assertEquals('new_name', $station->getName());
        $this->assertEquals('new_address', $station->getAddress());
        $this->assertEquals('new_source', $station->getSource());
        $this->assertEquals('new_source_id', $station->getSourceId());
        $this->assertEquals(2.0, $station->getLatitude());
        $this->assertEquals(2.0, $station->getLongitude());
        $this->assertEquals(2.0, $station->getAltitude());
        $this->assertEquals('new_photo_url', $station->getPhotoURL());
        $this->assertEquals(2, $station->getStatus());
    }
}
