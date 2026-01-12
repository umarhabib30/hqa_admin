<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'group',
        'description',
    ];

    /**
     * Get role permissions relationship
     */
    public function rolePermissions()
    {
        return $this->hasMany(\DB::table('role_permissions')->where('permission_id', $this->id));
    }
}
