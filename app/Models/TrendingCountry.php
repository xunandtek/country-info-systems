<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendingCountry extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'country_id';
    public $incrementing = false;

    protected $fillable = [
        'country_id',
        'views_24h',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
