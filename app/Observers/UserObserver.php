<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class UserObserver
{

    public function deleting(User $user)
    {   
        $act = Activity::where('causer_id', $user->id)->pluck('id');
        Activity::find($act)->map(function($act2){
            return $act2->delete();
        });
    }
}
