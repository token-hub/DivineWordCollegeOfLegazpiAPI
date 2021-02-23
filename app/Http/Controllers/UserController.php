<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserViewAnyRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

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

        $state = $user->fresh()->is_active == 1 ? 'activated' : 'deactivated';
        return response()->json(['message' => "User account was successfully {$state}"], 200);
    }

    public function destroy($userIds, UserViewAnyRequest $request)
    {
        $ids = array_map('intval', explode(',', $userIds));
       
        User::find($ids)->map(function($role){
            $role->delete();
        });

        return response()->json(['message' => 'User/s successfully deleted'], 200);
    }   
}
