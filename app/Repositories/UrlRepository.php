<?php

namespace App\Repositories;

use App\Models\Url;

class UrlRepository
{
    public function getAllUrls()
    {
        return Url::select('id', 'short_url', 'code')->get()->toArray();
    }

    public function getUrlById($id)
    {
        return Url::where('id', $id)->get()->toArray();
    }

    public function findByOriginalUrl($originalUrl)
    {
        return Url::where('original_url', $originalUrl)->first();
    }

    public function createUrl($data)
    {
        return Url::create($data)->toArray();
    }

    public function findByCode($code)
    {
        return Url::where('code', $code)->firstOrFail()->toArray();
    }
}
