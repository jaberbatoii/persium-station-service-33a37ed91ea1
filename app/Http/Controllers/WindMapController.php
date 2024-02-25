<?php
declare(strict_types = 1);

namespace Persium\Station\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Joselfonseca\LaravelTactician\CommandBusInterface;
use Persium\Station\Domain\CommandBus\GetWindMap\GetWindMapCommand;
use Persium\Station\Domain\CommandBus\GetWindMap\GetWindMapHandler;

class WindMapController extends BaseAPIController
{
    public function __construct(
        private readonly CommandBusInterface $bus,
    )
    {
    }

    public function index(): JsonResponse
    {
        $this->bus->addHandler(GetWindMapCommand::class, GetWindMapHandler::class);

        $data = $this->bus->dispatch(new GetWindMapCommand());

        return response()->json([
            'data' => $data
        ]);
    }
}
