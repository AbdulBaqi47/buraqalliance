<?php

namespace App\Accounts\Traits;

use App\Models\Tenant\Table_relation;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TableRelationTrait
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function table_relations() : HasMany
    {
        return $this->hasMany(Table_relation::class, 'source_id')->where('source_model', get_class($this));
    }
}
