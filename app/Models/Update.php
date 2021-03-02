<?php

namespace App\Models;

use App\Traits\ActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    use HasFactory;
    use ActivityLog;

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'updated_at' => 'datetime:M d, Y | h:i A',
        'created_at' => 'datetime:M d, Y | h:i A',
    ];

    protected $guarded = [];

    public function setCategoryAttribute($category)
    {
        $this->attributes['category'] = $category === 1 ? 'announcements' : 'news-and-events';
    }
}
