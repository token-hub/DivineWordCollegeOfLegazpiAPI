<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesController;
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

    Route::put('/password/update/{user}', [ChangePasswordController::class, 'update']);
    Route::put('/profile/{user}', [UserProfileController::class, 'update']);

    Route::get('/logs', [LogsController::class, 'index']);
    Route::get('/logs/{log}', [LogsController::class, 'show']);

    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::put('/permissions/{permission}', [PermissionController::class, 'update']);

    Route::post('/roles', [RolesController::class, 'store']);
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
