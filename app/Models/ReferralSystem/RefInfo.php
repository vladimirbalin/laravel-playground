<?php

namespace App\Models\ReferralSystem;

use Illuminate\Database\Eloquent\Model;

class RefInfo extends Model
{
    protected $casts = [
        'depth_1' => 'integer',
        'depth_2' => 'integer',
    ];

    protected $table = 'ref_info';

    protected $guarded = [];

    public $timestamps = false;
}
