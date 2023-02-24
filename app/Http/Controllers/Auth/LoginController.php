<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showAdminLoginForm()
    {
        return view('auth.login', [
            'url' => Config::get('constants.guards.admin'),
        ]);
    }

    protected function validator(Request $request)
    {
        return $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
    }

    //-----Admin Login coDe--
    public function adminLogin(Request $request)
    {

        if ($this->guardLogin($request, Config::get('constants.guards.admin'))) {

            return redirect()->intended('/admin');
        }
        return back()->withInput($request->only('email', 'remember'))->with('error', 'Email and Password not match. Please check and try with correct.');
    }
    //--All Users login functionality coDe--
    protected function guardLogin(Request $request, $guard)
    {
        $validate_Login = $this->validator($request);


        if ($guard == 'admin') {

            return Auth::guard($guard)->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
                $request->get('remember')
            );
        }

    }
    public function admin_logout(Request $request)
    {
        if (Auth::guard('admin')->check()) // this means that the admin was logged in.
        {
            Auth::guard('admin')->logout();
            return redirect()->route('login.admin');
        }

        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

}
