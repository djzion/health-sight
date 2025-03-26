<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryAssessment extends Model
{
    protected $fillable = [
        'user_id',
        'phc_id',
        'responses',
        'current_page',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phc()
    {
        return $this->belongsTo(Phc::class);
    }
}
