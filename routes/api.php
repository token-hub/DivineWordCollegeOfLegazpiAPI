<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\UserProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/contactUs', [ContactUsController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/password/update/{user}', [ChangePasswordController::class, 'update']);
    Route::put('/password/profile/{user}', [UserProfileController::class, 'update']);
});

// Route::post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);

//     return ['token' => $token->plainTextToken];
// });
