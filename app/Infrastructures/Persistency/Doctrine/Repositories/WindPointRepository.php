<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Persistency\Doctrine\Repositories;

use Doctrine\ORM\EntityManager;
use Persium\Station\Domain\Entities\Wind\WindData;
use Persium\Station\Domain\Entities\Wind\WindPoint;
use Persium\Station\Domain\Entities\Wind\WindPointRepositoryInterface;

class WindPointRepository implements WindPointRepositoryInterface
{
    private string $entity_name;
    public function __construct(
        private readonly EntityManager $entity_manager
    ) {
        $this->entity_name = WindPoint::class;
    }

    public function save(WindPoint $point): void
    {
        $this->entity_manager->persist($point);
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

    public function deleteAllWindData(): void
    {
        $this->entity_manager->createQuery('DELETE FROM ' . WindData::class)->execute();
    }

    public function getAllWithData(): array
    {
        $query = $this->entity_manager->createQuery(
            'SELECT p, d FROM ' . $this->entity_name . ' p
            LEFT JOIN p.data d
            ORDER BY p.id ASC'
        );
        return $query->getResult();
    }
}
