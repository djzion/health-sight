<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(RoleCategory::class, 'role_category_assignments', 'role_id', 'role_category_id');
   
    }

    public function roleCategories() {
        return $this->belongsToMany(RoleCategory::class, 'role_category_assignments')
            ->select('role_categories.id', 'role_categories.*'); // Explicitly specify the table for id
    }
}
