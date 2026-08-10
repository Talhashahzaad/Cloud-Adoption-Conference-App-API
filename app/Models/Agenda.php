<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'conference_id',

        'session_name',

        'presenter',

        'session_date',

        'duration',

        'place',

        'room',

    ];

    protected function casts(): array
    {
        return [

            'session_date' => 'date',

        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
}
