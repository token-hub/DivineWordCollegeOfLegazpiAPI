<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Models\User;

class UserProfileController extends Controller
{
    public function __invoke(User $user, UserProfileRequest $request)
    {
        $user->update($request->validated());

        $message = 'Nothing to update';

        if ($user->wasChanged()) {
            $message = 'Profile Successfully updated';
        }

        return response()->json(compact('message'), 200);
    }
}
