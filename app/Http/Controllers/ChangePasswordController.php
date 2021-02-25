<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;

class ChangePasswordController extends Controller
{
    public function __invoke(User $user, ChangePasswordRequest $request)
    {
        $user->update(['password' => $request['new_password']]);

        return response()->json(['message' => 'Password Successfully updated'], 200);
    }
}
