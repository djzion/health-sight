<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafecareResponses extends Model
{
    protected $fillable = [
        'user_id',
        'district_id',
        'lga_id',
        'phc_id',
        'safecare_id',
        'response',
        'comment',
        'assessment_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Safecare::class, 'safecare_id');
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }
    public function phc()
    {
        return $this->belongsTo(Phc::class);
    }
}
