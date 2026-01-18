<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryView extends Model
{
    protected $fillable = [
        'country_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
