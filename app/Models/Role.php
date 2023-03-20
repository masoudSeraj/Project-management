<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as OriginalRole;

class Role extends OriginalRole
{
    use HasFactory;
    protected $fillable = [
        'name',
        'is_admin',
        'guard_name',
        'updated_at',
        'created_at',
    ];
    public const ADMIN = ['admin', 'super admin', 'admininstrator'];
}
