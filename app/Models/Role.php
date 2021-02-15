<?php

namespace App\Models;

use App\Scopes\PermissionsScope;
use App\Traits\ActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use ActivityLog;

    protected $guarded = [];

    // protected $with = ['permissions'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PermissionsScope());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
