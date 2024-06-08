<?php

namespace Database\Factories;

use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Url>
 */
class UrlFactory extends Factory
{
    protected $model = Url::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $originalUrl = $this->faker->url;
        $urlParts = parse_url($originalUrl);
        $hashedUrl = substr(md5($urlParts['path'] . microtime()), 0, 6);
        $shortUrl = $urlParts['scheme'] . '://' . $urlParts['host']. '/' . $hashedUrl;

        return [
            'original_url' => $originalUrl,
            'short_url' => $shortUrl,
            'code' => $hashedUrl,
        ];
    }
}
