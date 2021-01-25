<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEmailVerification;
use App\Mail\SampleMail;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function sample()
    {
        // dispatch(new ProcessEmailVerification());
        ProcessEmailVerification::dispatch();
        // Mail::to('johnsuyang2119@gmail.com')->send(new SampleMail());
    }
}
