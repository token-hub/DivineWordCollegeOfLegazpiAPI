<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesController;
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

Route::post('/contactUs', [ContactUsController::class, 'store'])->middleware('throttle:contactUs');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // user
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::put('/users/{user}', [UserController::class, 'update']);

    // user profile
    Route::put('/password/update/{user}', [ChangePasswordController::class, 'update']);
    Route::put('/profile/{user}', [UserProfileController::class, 'update']);

    // user account status
    Route::put('/users/status/{user}', UserAccountStatusController::class);

    // logs
    Route::get('/logs', [LogsController::class, 'index']);
    Route::get('/logs/{log}', [LogsController::class, 'show']);

    // permissions
    Route::get('/permissions', PermissionController::class);

    // roles
    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::get('/roles/{role}', [RolesController::class, 'show']);
    Route::put('/roles/{role}', [RolesController::class, 'update']);
    Route::delete('/roles/{role}', [RolesController::class, 'destroy']);

    // updates
    Route::get('/updates', [UpdateController::class, 'index']);
    Route::post('/updates', [UpdateController::class, 'create']);
    Route::get('/updates/{update}', [UpdateController::class, 'show']);
    Route::put('/updates/{update}', [UpdateController::class, 'update']);
    Route::delete('/updates/{update}', [UpdateController::class, 'destroy']);
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
