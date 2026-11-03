<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [

        'conference_id',

        'title',

        'message',

        'scheduled_at',

        'sent_at',

        'created_by',

    ];

    protected function casts(): array
    {
        return [

            'scheduled_at' => 'datetime',

            'sent_at' => 'datetime',

        ];
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
