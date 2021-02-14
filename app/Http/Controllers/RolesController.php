<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolesRequest;
use App\Models\Role;
use Illuminate\Support\Arr;

class RolesController extends Controller
{
    public function store(RolesRequest $request)
    {
        Role::create(Arr::only($request->validated(), ['description']))
            ->permissions()
            ->attach($request->validated()['permission']);

        return response()->json(['message' => 'New role successfully added'], 201);
    }
}
