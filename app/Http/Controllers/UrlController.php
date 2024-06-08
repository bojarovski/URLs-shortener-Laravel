<?php

namespace App\Http\Controllers;

use App\Models\Url;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index()
    {
        $urls = Url::select('id', 'short_url', 'code')->get();

        return response()->json($urls);
    }

    public function show($id)
    {
        $urls = Url::where('id', $id)->get();

        return response()->json($urls);
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url'
        ]);

        $originalUrl = $request->input('original_url');
        $existingUrl = Url::where('original_url', $originalUrl)->first();

        if ($existingUrl) {
            return response()->json(['original_url' => $existingUrl->original_url]);
        }

        $apiKey = env('VIRUSTOTAL_API_KEY');
        $client = new Client();

        try {
            $response = $client->get(env('URL_API'), [
                'query' => [
                    'apikey' => $apiKey,
                    'resource' => $originalUrl
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            if (isset($result['positives']) && $result['positives'] > 0) {
                return response()->json(['error' => 'The URL is unsafe'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error checking URL safety'], 500);
        }


        $urlParts = parse_url($originalUrl);
        $hashedUrl = substr(md5($urlParts['path'] . microtime()), 0, 6);
        $shortUrl = $urlParts['scheme'] . '://' . $urlParts['host']. '/' . $hashedUrl;
        Url::create([
            'original_url' => $originalUrl,
            'short_url' => $shortUrl,
            'code'=> $hashedUrl,
        ]);

        return response()->json(['original_url' =>  $originalUrl]);
    }

    public function redirect($code)
    {
        $url = Url::where('code', $code)->firstOrFail();
        return response()->json($url);
    }
}
