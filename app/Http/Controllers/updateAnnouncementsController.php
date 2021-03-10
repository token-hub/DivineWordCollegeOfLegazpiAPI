<?php

namespace App\Http\Controllers;

use App\Http\Resources\UpdateResource;
use App\Models\Update;

class UpdateAnnouncementsController extends Controller
{
    public function __invoke()
    {
        $announcements = Update::where('category', 'announcements')->latest()->get();

        return UpdateResource::collection($announcements);
    }
}
