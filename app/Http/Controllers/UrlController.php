<?php

namespace App\Http\Controllers;

use App\Models\Url;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class UrlController extends Controller
{
     public function index()
    {
        $urls = Url::select('id', 'short_url')->get();

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
            $response = $client->get('https://www.virustotal.com/vtapi/v2/url/report', [
                'query' => [
                    'apikey' => $apiKey,
                    'resource' => $originalUrl
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            dd($result);
            if (isset($result['positives']) && $result['positives'] > 0) {
                return response()->json(['error' => 'The URL is unsafe'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error checking URL safety'], 500);
        }

        $shortUrl = Str::random(6);

        Url::create([
            'original_url' => $originalUrl,
            'short_url' => $shortUrl
        ]);

        return response()->json(['short_url' => url("/$shortUrl")]);
    }

    public function redirect($shortUrl)
    {
        $url = Url::where('short_url', $shortUrl)->firstOrFail();
        return response()->json($url);
    }
}
