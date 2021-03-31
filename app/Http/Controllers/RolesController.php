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
        $response = $role->permissions()->sync($request->validated()['permissions']);

        $isPermissionsWasNotChanged = empty($response['attached']) && empty($response['detached']) && empty($response['updated']);

        $message = ($role->wasChanged() || !$isPermissionsWasNotChanged)
        ? 'Role was successfully updated'
        : 'Nothing to update';

        return response()->json(compact('message'), 200);
    }

    public function destroy($roleIds, RoleDeleteRequest $request)
    {
        // $roleIDs is coming from an axios http request so it will be
        // string when it got here, so we need to turn it back to array,
        // before we use it

        $ids = array_map('intval', explode(',', $roleIds));

        Role::destroy($ids);

        return response()->json(['message' => 'Role/s was successfully deleted'], 200);
    }
}
