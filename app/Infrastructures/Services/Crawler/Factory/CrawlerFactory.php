<?php

declare(strict_types = 1);

namespace Persium\Station\Infrastructures\Services\Crawler\Factory;

use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;

class CrawlerFactory
{
    public static function create(
        string $source,
        StationRepositoryInterface $station_repository_interface
    ): CrawlerInterface
    {
        $crawler_class_name = 'Persium\\Station\\Infrastructures\\Services\\Crawler\\Factory\\' . $source . '\\' . 'Crawler';
        return new $crawler_class_name(
            $station_repository_interface,
        );
    }
}
