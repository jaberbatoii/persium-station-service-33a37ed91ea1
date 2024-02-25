<?php

namespace Tests\Unit\Domain\Helper;

use Persium\Station\Domain\Services\Helper\MapHelper;
use Tests\TestCase;

class MapHelperUnitTest extends TestCase
{
    public function testDistanceWithUnitM(){
        $map_helper = new MapHelper();
        $distance = $map_helper->distance(0, 0, 1, 1);
        $this->assertEquals(157241.8158675294, $distance);
    }

    public function testDistanceWithUnitK(){
        $map_helper = new MapHelper();
        $distance = $map_helper->distance(0, 0, 1, 1, 'K');
        $this->assertEquals(157.2418158675294, $distance);
    }

    public function testDistanceWithUnitN(){
        $map_helper = new MapHelper();
        $distance = $map_helper->distance(0, 0, 1, 1, 'N');
        $this->assertEquals(84.84748624244568, $distance);
    }
}
