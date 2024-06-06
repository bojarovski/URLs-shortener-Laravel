<?php

namespace Database\Seeders;

use App\Models\Url;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UrlSeeder extends Seeder
{
   public function run()
    {
        // Define the URLs you want to seed
        $urls = [
            'https://example.com/page1',
            'https://example.com/page2',
            'https://example.com/page3',
        ];

        // Populate the database with the URLs
        foreach ($urls as $url) {
            Url::create([
                'original_url' => $url,
                'short_url' => substr(md5($url), 0, 6) // Generate a short URL hash
            ]);
        }
    }
}
