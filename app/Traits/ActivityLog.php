<?php

namespace App\Traits;

use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityLog
{
    use LogsActivity;

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    protected static $logAttributes = ['*'];

    protected static $ignoreChangedAttributes = ['remember_token'];

    protected static $logAttributesToIgnore = [
        'id',
        'updated_at',
        'created_at',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    public function getDescriptionForEvent(string $eventName): string
    {
        $className = strtolower(class_basename(static::class));

        return "A {$className} was {$eventName}";
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($activity->causer_id == null) {
            $activity->causer_id = 1;
            $activity->causer_type = 'App\Models\User';
        }

        $activity->properties = $activity->properties->put('causer', \App\Models\User::find($activity->causer_id)->username);
    }
}
