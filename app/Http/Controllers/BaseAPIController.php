<?php

namespace Persium\Station\Http\Controllers;

class BaseAPIController
{
    protected function response($data, $status = 200, $headers = [], $options = 0)
    {
        return response()->json($data, $status, $headers, $options);
    }

    protected function responseCreated($data, $status = 201, $headers = [], $options = 0)
    {
        return response()->json($data, $status, $headers, $options);
    }

    protected function responseNoContent($status = 204, $headers = [], $options = 0)
    {
        return response()->json(null, $status, $headers, $options);
    }

    protected function responseBadRequest(string $message, $status = 400, $headers = [], $options = 0)
    {
        return response()->json([
            'error' => [
                'message' => $message,
            ],
        ], $status, $headers, $options);
    }
}
