<?php

namespace App\Http\Controllers;

use App\Http\Resources\UpdateResource;
use App\Models\Update;

class UpdateNewsAndEventsController extends Controller
{
    public function __invoke()
    {
        $newsAndEvents = Update::where('category', 'news-and-events')->latest()->get();

        return UpdateResource::collection($newsAndEvents);
    }
}
