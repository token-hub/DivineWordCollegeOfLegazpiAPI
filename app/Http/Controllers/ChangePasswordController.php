<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Support\Arr;

class ChangePasswordController extends Controller
{
    public function update(User $user, ChangePasswordRequest $request)
    {
        $user->update(Arr::only($request->validated(), ['new_password']));

        return response()->json(['messages' => 'Password Successfully updated'], 200);
    }
}
