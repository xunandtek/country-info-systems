<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CountryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $region = $request->string('region')->toString();

        $countries = Country::query()
            ->search($q)
            ->region($region)
            ->orderBy('name_common')
            ->paginate(25)
            ->withQueryString();

        $regions = Country::query()
            ->select('region')
            ->whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        $trending = Cache::remember('countries:trending', (int) env('TRENDING_CACHE_TTL', 300), function () {
            return Country::query()
                ->with('trending')
                ->whereHas('trending')
                ->orderByDesc(
                    \App\Models\TrendingCountry::select('views_24h')
                        ->whereColumn('trending_countries.country_id', 'countries.id')
                )
                ->limit(5)
                ->get();
        });

        return view('countries.index', compact('countries', 'regions', 'q', 'region', 'trending'));
    }

    public function show(string $code)
    {
        $code = strtoupper(trim($code));

        $country = Country::query()
            ->where('cca2', $code)
            ->orWhere('cca3', $code)
            ->with('borders')
            ->firstOrFail();

        CountryView::create([
            'country_id' => $country->id,
            'viewed_at' => now(),
        ]);

        return view('countries.show', compact('country'));
    }
}
