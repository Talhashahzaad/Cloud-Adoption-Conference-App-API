<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [

        'conference_id',

        'ticket_reference',

        'ticket_holder_name',

        'ticket_holder_email',

        'ticket_source',

        'status',

        'imported_by',

        'used_by_user_id',

        'used_at',

    ];

    protected function casts(): array
    {
        return [

            'used_at' => 'datetime',

        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }
}
