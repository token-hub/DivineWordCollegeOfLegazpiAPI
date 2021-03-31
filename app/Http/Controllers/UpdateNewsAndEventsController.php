<?php

namespace App\Http\Controllers;

use App\Http\Resources\UpdateCollection;
use App\Models\Update;

class UpdateNewsAndEventsController extends Controller
{
    public function __invoke()
    {
        $newsAndEvents = Update::where('category', 'news-and-events')->latest()->paginate(10);

        return new UpdateCollection($newsAndEvents);
    }
}
