<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PermissionsScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->with('permissions', function($q){
            $q->select(['description']);
        });
    }
}
