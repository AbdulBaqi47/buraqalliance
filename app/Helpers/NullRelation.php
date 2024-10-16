<?php

namespace App\Helpers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class NullRelation extends Relation {

    public function __construct() {}

    public function addConstraints() {}

    public function addEagerConstraints(array $models) {}

    public function initRelation(array $models, $relation) {}

    public function match(array $models, Collection $results, $relation) {
        return [];
    }

    public function getResults() {
        return null;
    }

}
