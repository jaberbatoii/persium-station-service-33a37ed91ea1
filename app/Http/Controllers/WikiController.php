<?php
declare(strict_types = 1);

namespace Persium\Station\Http\Controllers;

use Illuminate\Http\JsonResponse;

class WikiController extends BaseAPIController
{
    public function index(): JsonResponse
    {
        $wiki_data = config('wiki');

        return $this->response($wiki_data);
    }
}
