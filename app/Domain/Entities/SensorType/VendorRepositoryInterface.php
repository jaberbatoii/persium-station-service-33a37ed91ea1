<?php

namespace Persium\Station\Domain\Entities\SensorType;

interface VendorRepositoryInterface
{
    public function save(Vendor $vendor): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?Vendor;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?Vendor;
    public function count(array $criteria): int;
}
