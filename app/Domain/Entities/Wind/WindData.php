<?php

namespace Persium\Station\Domain\Entities\Wind;

use DateTimeInterface;

class WindData
{
    private int $id;
    public function __construct(
        private WindPoint $wind_point,
        private DateTimeInterface $timestamp,
        private float $speed,
        private float $direction,
    )
    {
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function setID(int $id): void
    {
        $this->id = $id;
    }

    public function getWindPoint(): WindPoint
    {
        return $this->wind_point;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    public function getDirection(): float
    {
        return $this->direction;
    }

    public function setSpeed(float $speed): void
    {
        $this->speed = $speed;
    }

    public function setDirection(float $direction): void
    {
        $this->direction = $direction;
    }

    public function setTimestamp(DateTimeInterface $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function setWindPoint(WindPoint $wind_point): void
    {
        $this->wind_point = $wind_point;
    }
}
