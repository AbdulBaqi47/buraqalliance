<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Get all of the employee_roles for the Role
     */
    public function employee_roles()
    {
        return $this->hasMany(EmployeeRole::class, 'role_id');
    }
}
