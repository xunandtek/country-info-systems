<?php

namespace App\Console\Commands;

use App\Models\TrendingCountry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecalculateTrendingCountries extends Command
{
    protected $signature = 'trending:recalculate {--limit=10}';
    protected $description = 'Recalculate trending countries by views in last 24 hours';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $since = now()->subDay();

        $rows = DB::table('country_views')
            ->select('country_id', DB::raw('COUNT(*) as views_24h'))
            ->where('viewed_at', '>=', $since)
            ->groupBy('country_id')
            ->orderByDesc('views_24h')
            ->limit($limit)
            ->get();

        DB::transaction(function () use ($rows) {
            TrendingCountry::query()->delete();

            foreach ($rows as $r) {
                TrendingCountry::create([
                    'country_id' => $r->country_id,
                    'views_24h' => (int) $r->views_24h,
                    'calculated_at' => now(),
                ]);
            }
        });

        Cache::forget('countries:trending');

        $this->info('Trending recalculated ✅');
        return self::SUCCESS;
    }
}
