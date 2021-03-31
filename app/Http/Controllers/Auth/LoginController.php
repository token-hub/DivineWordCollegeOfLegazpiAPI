<?php

namespace App\Http\Controllers\Auth;

use function App\Helpers\current_user;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $attempt = Auth::attempt(array_merge($this->credentials($request), ['is_active' => 1]));

        if ($attempt) {
            if (!current_user()->hasVerifiedEmail()) {
                return response()->json(['message' => 'Your account was not yet verified'], 200);
            }

            activity()
                ->on(current_user())
                ->by(current_user())
                ->withProperties(['causer' => current_user()->username])
                ->log('A user logged in');

            return response()->json(['message' => 'Successfully logged in.'], 200);
        }

        return  Auth::attempt($this->credentials($request))
            ? response()->json(['message' => 'Your account is Inactive'], 200)
            : $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        activity()
            ->on(current_user())
            ->by(current_user())
            ->withProperties(['causer' => current_user()->username])
            ->log('A user logged out');

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}
