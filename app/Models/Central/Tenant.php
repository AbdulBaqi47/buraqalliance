<?php

namespace App\Models\Central;

use App\Models\Central\BaseTenant as BaseTenant;
use MongoDB\Laravel\Relations\HasOne;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    # There was a field in this table "data", stancl/tenancy was appending fields to this attribute
    # but it also appends timestamps fields (created_at, updated_at), since DB is mongoDB
    # It converts dates to Array, and an error occured by laravel when it try to save the attribute as String
    # I wasted much time here to final solution is disable timestamp dates for now
    public $timestamps = false;

    public function domain() : HasOne {
        return $this->hasOne(Domain::class, 'tenant_id');
    }

}
