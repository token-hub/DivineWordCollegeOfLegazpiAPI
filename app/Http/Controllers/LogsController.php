<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogsResource;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function index()
    {
        return LogsResource::collection(Activity::all());
    }

    public function show($activity)
    {
        return  Activity::find($activity);
    }
}
