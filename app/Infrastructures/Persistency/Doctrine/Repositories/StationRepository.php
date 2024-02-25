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
use Persium\Station\Domain\ValueObjects\StationDataVO;

class StationRepository implements StationRepositoryInterface
{
    private string $entity_name;
    public function __construct(
        private readonly EntityManager $entity_manager
    ) {
        $this->entity_name = Station::class;
    }

    public function createQueryBuilder($alias, $index_by = null): QueryBuilder
    {
        return $this->entity_manager->createQueryBuilder()
            ->select($alias)
            ->from($this->entity_name, $alias, $index_by);
    }

    public function find($id, int $lock_mode = null, int $lock_version = null): ?Station
    {
        return $this->entity_manager->find($this->entity_name, $id, $lock_mode, $lock_version);
    }

    public function findOneByUuid(string $uuid): ?Station
    {
        return $this->findOneBy(['uuid' => $uuid]);
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

    public function findOneBy(array $criteria, ?array $order_by = null): ?Station
    {
        $persister = $this->entity_manager->getUnitOfWork()->getEntityPersister($this->entity_name);

        return $persister->load($criteria, null, null, [], null, 1, $order_by);
    }

    public function count(array $criteria) : int
    {
        return $this->entity_manager->getUnitOfWork()->getEntityPersister($this->entity_name)->count($criteria);
    }

    public function findOneByName(string $name): ?Station
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function flush(): void
    {
        $this->entity_manager->flush();
    }

    public function save(Station $station): void
    {
        $this->entity_manager->persist($station);
        $this->entity_manager->flush();
    }

    public function findOneSensorByType(Station $station, SensorType $sensor_type): ?StationSensor
    {
        return $this->entity_manager
            ->getRepository(StationSensor::class)
            ->findOneBy([
                'station' => $station,
                'type' => $sensor_type
            ]);
    }

    public function findNearestOneByLatAndLngInDistanceBySource(float $lat, float $lng, float $distance, array $sources): ?Station
    {
        $string_source = "'".implode("','", $sources)."'";
        //meter to mile
        $distance = $distance / 1609.344;

        $sql = "
            SELECT id, point(:lng, :lat) <@>  (point(longitude, latitude)::point) as distance
            FROM stations
            WHERE source in ($string_source)
            AND (point(:lng, :lat) <@> point(longitude, latitude)) < :distance
            ORDER BY distance
            LIMIT 1;
        ";

        $stmt = $this->entity_manager->getConnection()->prepare($sql);
        $result = $stmt->executeQuery([
            'lat' => $lat,
            'lng' => $lng,
            'distance' => $distance,
        ])->fetchAssociative();

        if (empty($result)) {
            return null;
        }

        return $this->find($result['id']);
    }

    public function getLatestDataTime(int $station_id): ?Carbon
    {
        $query = $this->entity_manager->createQuery(
            'SELECT MAX(s.timestamp) FROM Persium\Station\Domain\Entities\Station\StationSensorAQIData s
            JOIN s.sensor ss
            JOIN ss.station sss
            WHERE sss.id = :id'
        );
        $query->setParameter('id', $station_id);
        $result = $query->getSingleScalarResult();
        if ($result) {
            return Carbon::parse($result);
        }
        return null;
    }

    public function findInBoundWithStatus(
        float $lax_max, float $lat_min,
        float $lng_max, float $lng_min,
        array $statusIds
    ): array
    {
        $string_status = implode(",", $statusIds);

        $query = $this->entity_manager->createQuery("
            SELECT s
            FROM Persium\Station\Domain\Entities\Station\Station s
            WHERE s.latitude <= :lax_max
            AND s.latitude >= :lat_min
            AND s.longitude <= :lng_max
            AND s.longitude >= :lng_min
            AND s.status in ($string_status)
        ");
        $query->setParameter('lax_max', $lax_max);
        $query->setParameter('lat_min', $lat_min);
        $query->setParameter('lng_max', $lng_max);
        $query->setParameter('lng_min', $lng_min);
        return $query->getResult();
    }

    public function getLatestAQIDataByAQIType(int $station_id, int $aqi_type) :?StationAQIData
    {
        // get from StationAQIData
        $query = $this->entity_manager->createQuery("
            SELECT s.id, s.aqi, s.timestamp
            FROM Persium\Station\Domain\Entities\Station\StationAQIData s
            WHERE s.station_id = :station_id
            AND s.aqi_type = :aqi_type
            ORDER BY s.timestamp DESC
        ");
        $query->setParameter('station_id', $station_id);
        $query->setParameter('aqi_type', $aqi_type);
        $query->setMaxResults(1);
        $result = $query->getResult();
        if (empty($result)) {
            return null;
        }
        return $result[0];
    }

    public function getLatestDataByStationIdAndTime(int $station_id, Carbon $time): array
    {
        //Get from StationSensorData with raw query
        $sql = "
            SELECT s.timestamp, s.value, s.ugm3, s.ppb, st.name, ss.unit
            FROM station_sensor_data s
            JOIN station_sensors ss ON s.station_sensor_id = ss.id
            JOIN sensor_types st ON ss.sensor_type_id = st.id
            WHERE ss.station_id = :station_id
            AND s.timestamp = :time
        ";

        $stmt = $this->entity_manager->getConnection()->prepare($sql);
        $result = $stmt->executeQuery([
            'station_id' => $station_id,
            'time' => $time->toDateTimeString(),
        ])->fetchAllAssociative();

        if (empty($result)) {
            return [];
        }

        return $result;
    }

    public function getLatestDataByStationId(int $station_id): array
    {
        $latest_time = $this->getLatestDataTime($station_id);
        if (empty($latest_time)) {
            return [];
        }

        return $this->getLatestDataByStationIdAndTime($station_id, $latest_time);
    }
}
