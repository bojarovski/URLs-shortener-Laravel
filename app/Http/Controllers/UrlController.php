<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class UrlController extends Controller
{
     public function index()
    {
        $urls = Url::all();

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
            return response()->json(['short_url' => url("/{$existingUrl->short_url}")]);
        }

        $apiKey = env('GOOGLE_SAFE_BROWSING_API_KEY');
        $response = Http::post("https://safebrowsing.googleapis.com/v4/threatMatches:find?key=$apiKey", [
            'client' => ['clientId' => 'yourcompany', 'clientVersion' => '1.5.2'],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [['url' => $originalUrl]]
            ]
        ]);

        if ($response->json()) {
            return response()->json(['error' => 'The URL is unsafe'], 400);
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
        return redirect($url->original_url);
    }
}
