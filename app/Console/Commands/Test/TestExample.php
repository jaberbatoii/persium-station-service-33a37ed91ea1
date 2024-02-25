<?php

namespace Persium\Station\Console\Commands\Test;

use Illuminate\Console\Command;
use Persium\Station\Jobs\ExampleJob;

class TestExample extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "test:test";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl historic station data";

    public function handle()
    {
        dispatch(new ExampleJob);
    }
}
