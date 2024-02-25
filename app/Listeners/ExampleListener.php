<?php

declare(strict_types = 1);

namespace Persium\Station\Listeners;

use Persium\Station\Events\ExampleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExampleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(ExampleEvent $event): void
    {
        //
    }
}
