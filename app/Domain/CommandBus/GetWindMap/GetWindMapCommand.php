<?php
declare(strict_types=1);

namespace Persium\Station\Domain\CommandBus\GetWindMap;

class GetWindMapCommand
{
    public function __construct(
        public int $x_dimension = 17,
        public int $y_dimension = 17,
    )
    {
    }
}
