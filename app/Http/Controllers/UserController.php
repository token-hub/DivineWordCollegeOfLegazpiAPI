<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserViewAnyRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index(UserViewAnyRequest $request)
    {
        return User::all();
    }

    public function show(User $user, UserViewAnyRequest $request)
    {
        return $user;
    }

    public function update(User $user, UserUpdateRequest $request)
    {
        $user->update($request->validated());

        $response = $user->roles()->sync($request->validated()['roleIds']);

        $wasNotChanged = empty($response['attached']) && empty($response['detached']) && empty($response['updated']);

        $message = !$wasNotChanged
        ? 'User was succesfully updated'
        : 'Nothing to change';

        return response()->json(compact('message'), 200);
    }

    public function destroy($userIds, UserViewAnyRequest $request)
    {
        $ids = array_map('intval', explode(',', $userIds));

        User::find($ids)->map(function ($role) {
            $role->delete();
        });

        return response()->json(['message' => 'User/s successfully deleted'], 200);
    }
}
