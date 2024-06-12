<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    protected $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function index()
    {
        $urls = $this->urlService->getAllUrls();
        return response()->json($urls);
    }

    public function show($id)
    {
        $urls = $this->urlService->getUrlById($id);
        return response()->json($urls);
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url'
        ]);

        $originalUrl = $request->input('original_url');
        $result = $this->urlService->createShortUrl($originalUrl);

        if (is_array($result) && isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json(['original_url' => $originalUrl]);
    }

    public function redirect($code)
    {
        $url = $this->urlService->getUrlByCode($code);
        return response()->json($url);
    }
}
