<?php

namespace Persium\Station\Domain\Entities\Wind;

use Carbon\Carbon;
use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\ValueObjects\StationDataVO;

interface WindDataRepositoryInterface
{
    public function save(WindData $data): void;
    public function findAll(): array;
    public function deleteAll(): void;
}
