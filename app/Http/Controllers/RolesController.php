<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleDeleteRequest;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RolesResource;
use App\Models\Role;
use Illuminate\Support\Arr;

class RolesController extends Controller
{
    // I used the RoleDeleteRequest here cuz, index, delete, and show have
    // the same policy that doesn't require any rule validation :)

    public function index(RoleDeleteRequest $request)
    {
        return  RolesResource::collection(Role::latest()->get());
    }

    public function show(Role $role, RoleDeleteRequest $request)
    {
        return new RolesResource($role);
    }

    public function store(RoleStoreRequest $request)
    {
        Role::create(Arr::only($request->validated(), ['description']))
            ->permissions()
            ->attach($request->validated()['permissions']);

        return response()->json(['message' => 'New role successfully added'], 201);
    }

    public function update(Role $role, RoleUpdateRequest $request)
    {
        $role->update(Arr::only($request->validated(), ['description']));
        $role->permissions()->sync($request->validated()['permissions']);

        return response()->json(['message' => 'Role was successfully updated'], 200);
    }

    public function destroy($roleIDs, RoleDeleteRequest $request)
    {
        // $roleIDs is coming from an axios http request so it will be
        // string when it got here, so we need to turn it back to array,
        // before we use it

        $ids = array_map('intval', explode(',', $roleIDs));

        Role::find($ids)->map(function ($role) {
            $role->delete();
        });

        return response()->json(['message' => 'Role/s was successfully deleted'], 200);
    }
}
