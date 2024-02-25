<?php

namespace Persium\Station\Domain\Entities\Wind;

use Carbon\Carbon;
use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\ValueObjects\StationDataVO;

interface WindPointRepositoryInterface
{
    public function save(WindPoint $point): void;
    public function findAll(): array;
    public function deleteAll(): void;
    public function deleteAllWindData(): void;
    public function getAllWithData(): array;
}
