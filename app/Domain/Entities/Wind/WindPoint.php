<?php

namespace Persium\Station\Domain\Entities\Wind;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class WindPoint
{
    private int $id;
    private DateTimeInterface $created_at;
    private DateTimeInterface $updated_at;
    private DateTimeInterface $deleted_at;
    private Collection $data;
    public function __construct(
        private float $latitude,
        private float $longitude,
    )
    {
        $this->created_at = now();
        $this->updated_at = now();
        $this->data = new ArrayCollection();
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function setID(int $id): void
    {
        $this->id = $id;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updated_at;
    }

    public function getDeletedAt(): DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(DateTimeInterface $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getData(): Collection
    {
        return $this->data;
    }

    public function addData(WindData $data): void
    {
        $this->data->add($data);
    }
}
