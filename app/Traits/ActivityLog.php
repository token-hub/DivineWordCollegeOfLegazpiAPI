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

    public function getDescriptionForEvent(string $eventName): string
    {
        $className = strtolower(class_basename(static::class));

        return "A {$className} was {$eventName}";
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->properties = ['user' => \App\Models\User::find($activity->subject_id)->username];
    }
}
