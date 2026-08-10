<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'conference_id',
        'ticket_reference',
        'ticket_source',
        'ticket_metadata',
        'approval_status',
        'approval_remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'ticket_metadata' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
