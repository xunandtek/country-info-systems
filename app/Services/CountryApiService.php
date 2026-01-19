<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CountryApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.restcountries.base_url');
    }

    private function url(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function ttl(): int
    {
        return (int) (config('services.restcountries.cache_ttl') ?? 86400);
    }

    public function fetchAllCountries(): array
    {
        return Cache::remember('restcountries:all', $this->ttl(), function () {
            return $this->getJson(
                'all?fields=name,cca2,cca3,capital,region,subregion,population,flags,borders'
            );
        });
    }

    public function fetchCountryByCode(string $code): array
    {
        $code = strtoupper(trim($code));

        return Cache::remember("restcountries:code:{$code}", $this->ttl(), function () use ($code) {
            return $this->getJson(
                "alpha/{$code}?fields=name,cca2,cca3,capital,region,subregion,population,flags,borders"
            );
        });
    }

    public function searchCountries(string $query): array
    {
        $query = trim($query);
        $key = 'restcountries:search:' . md5(strtolower($query));

        return Cache::remember($key, 3600, function () use ($query) {
            return $this->getJson(
                'name/' . urlencode($query) . '?fields=name,cca2,cca3,capital,region,subregion,population,flags,borders'
            );
        });
    }

    private function getJson(string $path): array
    {
        try {
            $res = Http::withOptions(['verify' => false])
                ->retry(3, 200, function ($exception) {
                    return $exception instanceof ConnectionException;
                })
                ->timeout(10)
                ->acceptJson()
                ->get($this->url($path));


            if (!$res->successful()) {
                throw new RuntimeException("REST Countries API failed: {$res->status()}");
            }

            $json = $res->json();
            return is_array($json) ? $json : [];
        } catch (\Throwable $e) {
            throw new RuntimeException("Country API error: " . $e->getMessage(), 0, $e);
        }
    }
}
