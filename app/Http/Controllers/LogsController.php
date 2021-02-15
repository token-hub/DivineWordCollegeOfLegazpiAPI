<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogsResource;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function index()
    {
        return LogsResource::collection(Activity::latest()->get());
    }

    public function show($activity)
    {
        return new LogsResource(Activity::find($activity));
    }
}
