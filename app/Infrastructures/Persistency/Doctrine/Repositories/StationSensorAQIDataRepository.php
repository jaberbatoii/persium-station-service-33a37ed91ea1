<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Persistency\Doctrine\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Persium\Station\Domain\Entities\SensorType\CrossSensitivity;
use Persium\Station\Domain\Entities\Station\StationAQIData;
use Persium\Station\Domain\Entities\Station\StationSensorAQIData;
use Persium\Station\Domain\Entities\Station\StationSensorAQIDataRepositoryInterface;
use Persium\Station\Domain\Entities\Station\StationSensorData;
use Doctrine\ORM\EntityManager;

class StationSensorAQIDataRepository implements StationSensorAQIDataRepositoryInterface
{
    private string $entity_name;
    public function __construct(
        private readonly EntityManager $entity_manager
    ) {
        $this->entity_name = StationSensorAQIData::class;
    }

    public function createQueryBuilder($alias, $index_by = null): QueryBuilder
    {
        return $this->entity_manager->createQueryBuilder()
            ->select($alias)
            ->from($this->entity_name, $alias, $index_by);
    }

    public function find($id, int $lock_mode = null, int $lock_version = null): ?StationSensorAQIData
    {
        return $this->entity_manager->find($this->entity_name, $id, $lock_mode, $lock_version);
    }

    public function findAll() : array
    {
        return $this->findBy([]);
    }

    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array
    {
        $persister = $this->entity_manager->getUnitOfWork()->getEntityPersister($this->entity_name);

        return $persister->loadAll($criteria, $order_by, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $order_by = null): ?StationSensorAQIData
    {
        $persister = $this->entity_manager->getUnitOfWork()->getEntityPersister($this->entity_name);

        return $persister->load($criteria, null, null, [], null, 1, $order_by);
    }

    public function count(array $criteria) : int
    {
        return $this->entity_manager->getUnitOfWork()->getEntityPersister($this->entity_name)->count($criteria);
    }

    public function save(StationSensorAQIData $station_sensor_aqi_data): void
    {
        $this->entity_manager->persist($station_sensor_aqi_data);
    }

    public function flush(): void
    {
        $this->entity_manager->flush();
    }
}
