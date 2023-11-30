<?php

namespace App\Models\Lector;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diploma extends Model
{
    use HasFactory;

    protected $table = 'diplomas';

    protected $fillable = [
        'preview_picture',
    ];

    public function lector(): BelongsTo
    {
        return $this->belongsTo(Lector::class);
    }
}
