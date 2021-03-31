<?php

namespace App\Http\Controllers;

use App\Http\Resources\UpdateCollection;
use App\Models\Update;

class UpdateAnnouncementsController extends Controller
{
    public function __invoke()
    {
        $announcements = Update::where('category', 'announcements')->latest()->paginate(10);

        return new UpdateCollection($announcements);
    }
}
