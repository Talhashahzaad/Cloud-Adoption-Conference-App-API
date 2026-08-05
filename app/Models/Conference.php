<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'logo',
        'cover_image',
        'start_date',
        'end_date',
        'registration_start_date',
        'registration_end_date',
        'venue',
        'google_map_url',
        'status',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
