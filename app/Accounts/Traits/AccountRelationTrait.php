<?php

namespace App\Accounts\Traits;

trait AccountRelationTrait
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account_relation()
    {
        return $this->hasOne('App\Accounts\Models\Account_relation', 'subject_id')->where('subject_type',get_class($this));
    }
}
