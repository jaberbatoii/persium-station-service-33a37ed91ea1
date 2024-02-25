<?php

namespace Tests\Feature\Route;

use Persium\Station\Http\Controllers\WikiController;
use Tests\TestCase;

class WikiControllerTest extends TestCase
{
    public function testWikiIndexSuccess()
    {
        $wiki_controller = new WikiController();
        $response = $wiki_controller->index();
        $expected_response = config('wiki');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expected_response), $response->content());
    }
}
