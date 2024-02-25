<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Persistency\Doctrine\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\QueryBuilder;
use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\Entities\Station\Station;
use Doctrine\ORM\EntityManager;
use Persium\Station\Domain\Entities\Station\StationAQIData;
use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensor;
use Persium\Station\Domain\Entities\Wind\WindData;
use Persium\Station\Domain\Entities\Wind\WindDataRepositoryInterface;
use Persium\Station\Domain\Entities\Wind\WindPointRepositoryInterface;
use Persium\Station\Domain\ValueObjects\StationDataVO;

class WindDataRepository implements WindDataRepositoryInterface
{
    private string $entity_name;
    public function __construct(
        private readonly EntityManager $entity_manager
    ) {
        $this->entity_name = WindData::class;
    }

    public function save(WindData $data): void
    {
        $this->entity_manager->persist($data);
        $this->entity_manager->flush();
    }

    public function findAll(): array
    {
        return $this->entity_manager->getRepository($this->entity_name)->findAll();
    }

    public function deleteAll(): void
    {
        $this->entity_manager->createQuery('DELETE FROM ' . $this->entity_name)->execute();
    }
}
