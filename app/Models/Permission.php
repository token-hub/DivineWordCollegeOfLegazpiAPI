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

    protected $casts = [
        'created_at' => 'string',
    ];

    // remove this later
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
