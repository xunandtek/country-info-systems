<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\CountryApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCountries extends Command
{
    protected $signature = 'countries:sync {--fresh-borders : Rebuild border relationships}';
    protected $description = 'Sync countries from REST Countries API into local database';

    public function handle(CountryApiService $api): int
    {
        $this->info('Fetching countries from REST Countries API...');
        $data = $api->fetchAllCountries();

        if (!is_array($data)) {
            $this->error('Unexpected response format.');
            return self::FAILURE;
        }

        $this->info('Upserting countries...');
        foreach ($data as $row) {
            $cca2 = $row['cca2'] ?? null;
            $cca3 = $row['cca3'] ?? null;

            if (!$cca2 || !$cca3) continue;

            Country::updateOrCreate(
                ['cca3' => $cca3],
                [
                    'cca2' => $cca2,
                    'name_common' => $row['name']['common'] ?? $cca3,
                    'name_official' => $row['name']['official'] ?? null,
                    'capital' => isset($row['capital'][0]) ? $row['capital'][0] : null,
                    'region' => $row['region'] ?? null,
                    'subregion' => $row['subregion'] ?? null,
                    'population' => $row['population'] ?? null,
                    'flag_png' => $row['flags']['png'] ?? null,
                    'flag_svg' => $row['flags']['svg'] ?? null,
                ]
            );
        }

        $this->info('Countries synced ✅');

        if ($this->option('fresh-borders')) {
            $this->info('Rebuilding borders pivot...');
            $this->syncBorders($data);
        } else {
            $this->info('Skipping borders (run with --fresh-borders to rebuild).');
        }

        $this->info('Done ✅');
        return self::SUCCESS;
    }

    private function syncBorders(array $data): void
    {
        DB::transaction(function () use ($data) {
            DB::table('country_borders')->truncate();

            $map = Country::query()
                ->select(['id', 'cca3'])
                ->get()
                ->keyBy('cca3');

            foreach ($data as $row) {
                $cca3 = $row['cca3'] ?? null;
                if (!$cca3) continue;

                $country = $map->get($cca3);
                if (!$country) continue;

                $borders = $row['borders'] ?? [];
                if (!is_array($borders)) continue;

                $insert = [];
                foreach ($borders as $borderCca3) {
                    $border = $map->get($borderCca3);
                    if (!$border) continue;

                    $insert[] = [
                        'country_id' => $country->id,
                        'border_country_id' => $border->id,
                    ];
                }

                if (!empty($insert)) {
                    DB::table('country_borders')->insertOrIgnore($insert);
                }
            }
        });
    }
}
