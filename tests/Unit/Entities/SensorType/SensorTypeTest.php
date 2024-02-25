<?php

declare(strict_types = 1);

namespace Tests\Unit\Entities\SensorType;

use Persium\Station\Domain\Entities\SensorType\SensorType;
use Tests\TestCase;

class SensorTypeTest extends TestCase
{
    public function testConstruct()
    {
        // test construct for SensorType
        $sensor_type = new SensorType(
            name: 'name',
            display_name: 'display_name',
            type: 1,
            e_multi: 1.0,
            e_pow_multi: 1.0,
            m_multi: 1.0,
            molar_mass: 1.0,
            constant: 1.0,
            p_nom: 1.0,
        );
        $this->assertEquals('name', $sensor_type->getName());
        $this->assertEquals('display_name', $sensor_type->getDisplayName());
        $this->assertEquals(1.0, $sensor_type->getEMulti());
        $this->assertEquals(1.0, $sensor_type->getEPowMulti());
        $this->assertEquals(1.0, $sensor_type->getMMulti());
        $this->assertEquals(1.0, $sensor_type->getMolarMass());
        $this->assertEquals(1.0, $sensor_type->getConstant());
        $this->assertEquals(1.0, $sensor_type->getPNom());
    }

    public function testSet()
    {
        // test set for SensorType
        $sensor_type = new SensorType(
            name: 'name',
            display_name: 'display_name',
            type: 1,
            e_multi: 1.0,
            e_pow_multi: 1.0,
            m_multi: 1.0,
            molar_mass: 1.0,
            constant: 1.0,
            p_nom: 1.0,
        );
        $sensor_type->setName('name2');
        $sensor_type->setDisplayName('display_name2');
        $sensor_type->setEMulti(2.0);
        $sensor_type->setEPowMulti(2.0);
        $sensor_type->setMMulti(2.0);
        $sensor_type->setMolarMass(2.0);
        $sensor_type->setConstant(2.0);
        $sensor_type->setPNom(2.0);
        $this->assertEquals('name2', $sensor_type->getName());
        $this->assertEquals('display_name2', $sensor_type->getDisplayName());
        $this->assertEquals(2.0, $sensor_type->getEMulti());
        $this->assertEquals(2.0, $sensor_type->getEPowMulti());
        $this->assertEquals(2.0, $sensor_type->getMMulti());
        $this->assertEquals(2.0, $sensor_type->getMolarMass());
        $this->assertEquals(2.0, $sensor_type->getConstant());
        $this->assertEquals(2.0, $sensor_type->getPNom());
    }
}
