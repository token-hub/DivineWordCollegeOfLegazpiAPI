<?php

namespace App\Models;

use App\Traits\ActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    use ActivityLog;

    protected $guarded = [];

    protected $hidden = ['pivot'];

    protected $casts = [
        'created_at' => 'string',
    ];
}
