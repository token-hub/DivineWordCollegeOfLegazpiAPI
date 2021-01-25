<?php

use App\Http\Controllers\HomeController;
use App\Jobs\ProcessEmailVerification;
use App\Mail\SampleMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // Mail::to('johnsuyang2119@gmail.com')->send(new SampleMail());

    return view('welcome');
});

Auth::routes(['verify' => 'true']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/sample', function () {
//     Mail::to('johnsuyang2119@gmail.com')->send(new SampleMail());
//     //     ->delay(now()->addSeconds(5));

//     // dispatch(new ProcessEmailVerification());
//     // ProcessEmailVerification::dispatch()
//     //     ->delay(now()->addSeconds(10));
// });

Route::get('/sample', [HomeController::class, 'sample']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('verified');
