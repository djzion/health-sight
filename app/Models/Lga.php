<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    protected $fillable = [
        'name', 'district_id',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function phc()
    {
        return $this->hasMany(Phc::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
