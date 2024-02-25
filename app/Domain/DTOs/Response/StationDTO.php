<?php
declare(strict_types = 1);

namespace Persium\Station\Domain\DTOs\Response;

use Persium\Station\Domain\Entities\Station\Station;

class StationDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly string $address,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $source,
        public readonly string $source_id,
        public readonly ?string $photo_url,
        public readonly string $installed_at,
    )
    {
    }

    public static function createFromEntity(Station $station): self
    {
        return new self(
            uuid: $station->getUUID(),
            name: $station->getName(),
            address: $station->getAddress(),
            latitude: $station->getLatitude(),
            longitude: $station->getLongitude(),
            source: $station->getSource(),
            source_id: $station->getSourceID(),
            photo_url: $station->getPhotoURL(),
            installed_at: $station->getInstalledAt()->format('Y-m-d H:i:s'),
        );
    }
}
