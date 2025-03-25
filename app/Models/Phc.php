<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phc extends Model
{
    protected $fillable = [
        'name', 'lga_id', 'district_id',
    ];

    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
