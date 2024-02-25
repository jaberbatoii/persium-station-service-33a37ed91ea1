<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\ValueObjects;

use Persium\Station\Domain\Entities\Station\Station;
use DateTimeInterface;

class StationAQIDataVO
{
    public function __construct(
        private readonly int $value,
        private readonly int $type,
        private readonly string $factor,
        private readonly DateTimeInterface $timestamp,
    ) {
    }

    public function getFactor(): string
    {
        return $this->factor;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }
}
