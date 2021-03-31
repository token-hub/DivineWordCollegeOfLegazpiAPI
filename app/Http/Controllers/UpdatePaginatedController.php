<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateViewRequest;
use App\Http\Resources\UpdateCollection;
use App\Models\Update;

class UpdatePaginatedController extends Controller
{
    public function __invoke(UpdateViewRequest $request)
    {
        return new UpdateCollection(Update::latest()->paginate(10));
    }
}
