<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\SlideReorderController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserAccountStatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
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

Route::post('/contactUs', ContactUsController::class)->middleware('throttle:contactUs');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // users
    Route::apiResource('users', UserController::class, ['except' => ['store']]);

    // user profile
    Route::put('/password/update/{user}', ChangePasswordController::class);
    Route::put('/profile/{user}', UserProfileController::class);

    // user account status
    Route::put('/users/status/{user}', UserAccountStatusController::class);

    // logs
    Route::get('/logs', [LogsController::class, 'index']);
    Route::get('/logs/{log}', [LogsController::class, 'show']);

    // permissions
    Route::get('/permissions', PermissionController::class);

    // roles
    Route::apiResource('roles', RolesController::class);

    // updates
    Route::apiResource('updates', UpdateController::class);

    // slides
    Route::get('/slides', [SlideController::class, 'index']);
    Route::post('/slides', [SlideController::class, 'store']);
    Route::delete('/slides/{slide}', [SlideController::class, 'destroy']);

    // slides Reorder
    Route::put('/slides/reorder', SlideReorderController::class);
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
