<?php

namespace App\Services;

use App\Repositories\UrlRepository;
use GuzzleHttp\Client;

class UrlService
{
    protected $client;
    protected $apiKey;
    protected $urlApi;
    protected $urlRepository;

    public function __construct(Client $client, UrlRepository $urlRepository)
    {
        $this->client = $client;
        $this->apiKey = '7651f064b9800856e3d908db08d02de1efc30706657cb08d8e2313ff43e35a1e';
        $this->urlApi = 'https://www.virustotal.com/vtapi/v2/url/report';
        $this->urlRepository = $urlRepository;
    }

    public function getAllUrls()
    {
        return $this->urlRepository->getAllUrls();
    }

    public function getUrlById($id)
    {
        return $this->urlRepository->getUrlById($id);
    }

    public function createShortUrl($originalUrl)
    {
        $existingUrl = $this->urlRepository->findByOriginalUrl($originalUrl);

        if ($existingUrl) {
            return $existingUrl->original_url;
        }

        $response = $this->client->get($this->urlApi, [
            'query' => [
                'apikey' => $this->apiKey,
                'resource' => $originalUrl
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if (isset($result['positives']) && $result['positives'] > 0) {
            return ['error' => 'The URL is unsafe'];
        }

        $urlParts = parse_url($originalUrl);
        $hashedUrl = substr(md5($urlParts['path'] . microtime()), 0, 6);
        $shortUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . '/' . $hashedUrl;

        $data = [
            'original_url' => $originalUrl,
            'short_url' => $shortUrl,
            'code'=> $hashedUrl,
        ];

        return $this->urlRepository->createUrl($data);
    }

    public function getUrlByCode($code)
    {
        return $this->urlRepository->findByCode($code);
    }
}
