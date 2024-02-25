<?php

namespace Persium\Station\Infrastructures\Services\Crawler\Factory;

use Persium\Station\Domain\Entities\Station\StationRepositoryInterface;

class CrawlerBase
{
    protected string $config_name = '';
    protected array $config = [];
    protected array $child_sources = [];
    protected string $base_url = '';
    protected string $auth_user = '';
    protected string $auth_password = '';
    protected string $auth_secret = '';
    public function __construct(
        protected StationRepositoryInterface $station_repository_interface,
    ) {
        $config = config('crawler.'.$this->config_name);
        if (empty($config) || !is_array($config) || !isset($config['base_url'])) {
            throw new \Exception('Config not found');
        }
        $this->base_url = $config['base_url'];
        $this->auth_user = $config['auth_user'] ?? '';
        $this->auth_password = $config['auth_password'] ?? '';
        $this->child_sources = $config['sources'] ?? '';
        $this->auth_secret = $config['auth_secret'] ?? '';
        $this->config = $config;
    }
}
