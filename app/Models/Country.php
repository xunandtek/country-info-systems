<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'cca2',
        'cca3',
        'name_common',
        'name_official',
        'capital',
        'region',
        'subregion',
        'population',
        'flag_png',
        'flag_svg',
    ];

    public function borders()
    {
        return $this->belongsToMany(
            Country::class,
            'country_borders',
            'country_id',
            'border_country_id'
        );
    }

    public function borderedBy()
    {
        return $this->belongsToMany(
            Country::class,
            'country_borders',
            'border_country_id',
            'country_id'
        );
    }

    public function views()
    {
        return $this->hasMany(CountryView::class);
    }

    public function trending()
    {
        return $this->hasOne(TrendingCountry::class);
    }

    // ---- Scopes ----

    public function scopeRegion(Builder $query, ?string $region): Builder
    {
        if (!$region) return $query;
        return $query->where('region', $region);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        $term = trim($term);
        if ($term === '') return $query;

        // MySQL FULLTEXT search (fast + indexed)
        return $query->whereRaw(
            "MATCH(name_common, capital) AGAINST (? IN BOOLEAN MODE)",
            [$term . '*']
        );
    }
}
