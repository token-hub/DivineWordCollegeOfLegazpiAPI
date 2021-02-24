<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAccountStatusUpdateRequest;
use App\Models\User;

class UserAccountStatusController extends Controller
{
    public function __invoke(User $user, UserAccountStatusUpdateRequest $request)
    {
        $user->update($request->validated());

        $state = $user->fresh()->is_active == 1 ? 'activated' : 'deactivated';

        return response()->json(['message' => "User account was successfully {$state}"], 200);
    }
}
