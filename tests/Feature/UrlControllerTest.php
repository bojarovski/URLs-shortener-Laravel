<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Services\UrlService;
use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class UrlControllerTest extends TestCase
{
    protected $urlServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlServiceMock = Mockery::mock(UrlService::class);
    }

    public function testIndex()
    {
        $this->urlServiceMock->shouldReceive('getAllUrls')->andReturn(['url1', 'url2']);

        $urlController = new UrlController($this->urlServiceMock);

        $response = $urlController->index();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['url1', 'url2'], json_decode($response->getContent(), true));
    }

    public function testStoreWithValidUrl()
    {
        // Mocking the request data
        $requestData = [
            'original_url' => 'https://example.com'
        ];

        // Mocking the response from the UrlService
        $this->urlServiceMock
            ->shouldReceive('createShortUrl')
            ->with($requestData['original_url'])
            ->andReturn(['original_url' => $requestData['original_url']]);

        // Creating an instance of the UrlController with mocked dependency
        $urlController = new UrlController($this->urlServiceMock);

        // Creating a mock request
        $request = Request::create('/store', 'POST', $requestData);

        // Testing the store method
        $response = $urlController->store($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['original_url' => $requestData['original_url']], json_decode($response->getContent(), true));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
