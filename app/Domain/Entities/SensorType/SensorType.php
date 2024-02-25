<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\SensorType;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\LazyCriteriaCollection;
use Persium\Station\Domain\Entities\Station\StationSensor;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Str;

class SensorType
{

    const TYPE_POLLUTANT = 1;
    const TYPE_ATMOSPHERIC = 2;
    const TYPE_ENGINEERING = 3;

    private int $id;
    private string $uuid;
    private DateTimeInterface $created_at;
    private DateTimeInterface $updated_at;
    private ?DateTimeInterface $deleted_at = null;

    private Collection $sensors;
    private Collection $cross_sensitivities;

    public function __construct(
        private string $raw_name,
        private string $name,
        private string $display_name,
        private int $type,
        private float $e_multi,
        private float $e_pow_multi,
        private float $m_multi,
        private float $molar_mass,
        private float $constant,
        private float $p_nom,
    ) {
        $this->sensors = new ArrayCollection();
        $this->cross_sensitivities = new ArrayCollection();
        $this->uuid = Str::uuid()->toString();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = $this->created_at;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getRawName(): string
    {
        return $this->raw_name;
    }

    public function setRawName(string $raw_name): void
    {
        $this->raw_name = $raw_name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name): void
    {
        $this->display_name = $display_name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getEMulti(): float
    {
        return $this->e_multi;
    }

    public function setEMulti(float $e_multi): void
    {
        $this->e_multi = $e_multi;
    }

    public function getEPowMulti(): float
    {
        return $this->e_pow_multi;
    }

    public function setEPowMulti(float $e_pow_multi): void
    {
        $this->e_pow_multi = $e_pow_multi;
    }

    public function getMMulti(): float
    {
        return $this->m_multi;
    }

    public function setMMulti(float $m_multi): void
    {
        $this->m_multi = $m_multi;
    }

    public function getMolarMass(): float
    {
        return $this->molar_mass;
    }

    public function setMolarMass(float $molar_mass): void
    {
        $this->molar_mass = $molar_mass;
    }

    public function getConstant(): float
    {
        return $this->constant;
    }

    public function setConstant(float $constant): void
    {
        $this->constant = $constant;
    }

    public function getPNom(): float
    {
        return $this->p_nom;
    }

    public function setPNom(float $p_nom): void
    {
        $this->p_nom = $p_nom;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(): void
    {
        $this->deleted_at = new DateTime();
    }

    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(StationSensor $sensor): void
    {
        if ($this->sensors->exists(function ($key, $element) use($sensor) {
            return $element->getName() === $sensor->getName();
        })){
            return;
        }
        $this->sensors->add($sensor);
        $sensor->setType($this);
    }

    public function setCrossSensitivities(?Collection $cross_sensitivities): void
    {
        $this->cross_sensitivities = $cross_sensitivities;
    }

    public function getCrossSensitivities(): Collection
    {
        return $this->cross_sensitivities;
    }

    public function isPollutant(): bool
    {
        return $this->type === self::TYPE_POLLUTANT;
    }

    public function isAtmospheric(): bool
    {
        return $this->type === self::TYPE_ATMOSPHERIC;
    }

    public function isEngineering(): bool
    {
        return $this->type === self::TYPE_ENGINEERING;
    }
}
