<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDeleteRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Requests\UpdateUpdateRequest;
use App\Http\Requests\UpdateViewRequest;
use App\Models\Update;

class UpdateController extends Controller
{
    public function index(UpdateViewRequest $request)
    {
        return Update::all();
    }

    public function show(Update $update, UpdateViewRequest $request)
    {
        return $update;
    }

    public function store(UpdateStoreRequest $request)
    {
        Update::create($request->validated());

        return response()->json(['message' => 'Update was successfully added'], 201);
    }

    public function update(Update $update, UpdateUpdateRequest $request)
    {
        $update->update($request->validated());

        $message = $update->wasChanged()
        ? 'Update was successfully updated'
        : 'Nothing to update';

        return response()->json(compact('message'), 200);
    }

    public function destroy(Update $update, UpdateDeleteRequest $request)
    {
        $update->delete();

        return response()->json(['message' => 'Update was successfully deleted']);
    }
}
