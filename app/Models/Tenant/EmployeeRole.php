<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class EmployeeRole extends Model
{
    use HasFactory;

    /**
     * Get the role that owns the EmployeeRole
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
