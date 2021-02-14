<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return PermissionResource::collection(Permission::latest()->get());
    }

    public function update(Permission $permission, PermissionRequest $request)
    {
        $permission->update($request->validated());

        $message = $permission->wasChanged()
            ? 'Permission updated successfully'
            : 'Nothing to update';

        return response()->json(compact('message'));
    }
}
