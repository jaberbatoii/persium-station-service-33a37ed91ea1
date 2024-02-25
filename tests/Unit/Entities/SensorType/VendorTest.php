<?php

declare(strict_types = 1);

namespace Tests\Unit\Entities\SensorType;

use Persium\Station\Domain\Entities\SensorType\Vendor;
use Tests\TestCase;

class VendorTest extends TestCase
{
    public function testConstruct()
    {
        $vendor = new Vendor(
            name: 'name',
            display_name: 'display_name'
        );

        $this->assertNotNull($vendor->getUUID());
        $this->assertNotNull($vendor->getCreatedAt());
        $this->assertNotNull($vendor->getUpdatedAt());
        $this->assertNull($vendor->getDeletedAt());
        $this->assertEquals('name', $vendor->getName());
        $this->assertEquals('display_name', $vendor->getDisplayName());
    }

    public function testSetters()
    {
        $vendor = new Vendor(
            name: 'name',
            display_name: 'display_name'
        );

        $vendor->setName('new_name');
        $vendor->setDisplayName('new_display_name');

        $this->assertEquals('new_name', $vendor->getName());
        $this->assertEquals('new_display_name', $vendor->getDisplayName());
    }
}
