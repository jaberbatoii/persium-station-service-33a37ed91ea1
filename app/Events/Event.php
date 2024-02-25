<?php

declare(strict_types = 1);

namespace Persium\Station\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}
