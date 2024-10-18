<?php

namespace App\Models\Tenant;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use function PHPUnit\Framework\throwException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'user_type',
        'designation',
        'password',
        'type',
        'props',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * O-to-M relation for granted routes of an employee
     *
     * @var array
     */
    public function granted_routes()
    {
        return $this->hasMany('App\Models\Tenant\Employee_route', 'employee_id');
    }


    /**
     * O-to-M relation for granted routes of an employee
     *
     * @var array
     */
    public function employee_roles()
    {
        return $this->hasMany(EmployeeRole::class, 'employee_id');
    }

    /**
     * O-to-M relation for granted access of accounts of an employee
     *
     * @var array
    */
    public function account_access()
    {
        return $this->hasMany('App\Accounts\Models\Account_access', 'user_id');
    }

    /**
     * A solution to hasManyThrough relation for mongodb
     *
     * @var array
    */
    public function getAccountsAttribute()
    {
        $account_ids = $this->account_access()->select('account_id')->get()->pluck('account_id');

        return \App\Accounts\Models\Account::whereIn('_id', $account_ids)->get();
    }

    /**
     * Will extract employee role against a tag
     *
     * @param string $tag module name like entry_access
    */
    public function getCustomRole($tag) : object|null
    {
        # Eager load role, so it will execute only 1st time
        $this->loadMissing('employee_roles.role');

        # Find role against tag, if not found, deny access
        $employee_role = $this->employee_roles->first(function($employee_role) use ($tag){
            return $employee_role->role->tag === $tag;
        });

        if(!isset($employee_role)) return null;

        $employee_role = $employee_role->toArray();

        $employee_role = json_decode(json_encode($employee_role));

        if(!isset($employee_role->access_scope)) $employee_role->access_scope=null;
        if(!isset($employee_role->access_data)) $employee_role->access_data=null;

        return $employee_role;
    }

    /**
     * Scope a query to only include employees (non-admin).
     */
    public function scopeEmployees($query): void
    {
        $query->where('type', '!=', 'su');
    }

    /**
     * Check if user is admin
     */
    public function getIsAdminAttribute($query): bool
    {
        return $this->type === 'su';
    }

    /**
     * Get all of the sim_entities for the Employee
     */
    public function sim_entities()
    {
        return $this->hasMany(SimEntity::class, 'source_id')->where('source_model', User::class);
    }

    /**
     * Get all of the tasks for the Employee
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }

}
