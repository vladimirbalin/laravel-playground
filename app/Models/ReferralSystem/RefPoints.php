<?php

namespace App\Models\ReferralSystem;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefPoints extends Model
{
    protected $guarded = [];
    protected $casts = [
        'points' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
