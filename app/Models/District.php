<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name',];


    public function lgas()
    {
        return $this->hasMany(Lga::class);
    }


}
