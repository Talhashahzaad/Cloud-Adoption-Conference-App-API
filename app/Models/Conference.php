<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Conference extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'conference_date',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_state',
        'active_start_date',
        'active_end_date',
        'google_map_url',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'conference_date' => 'date',
            'active_start_date' => 'date',
            'active_end_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}