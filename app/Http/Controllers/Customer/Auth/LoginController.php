<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/customer/dashboard';

    public function __construct()
    {
        $this->middleware('guest:customer')->except('logout');
    }

    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    protected function guard()
    {
        return auth()->guard('customer');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->update(['last_login_at' => now()]);

        if ($request->filled('redirect') && str_starts_with($request->redirect, url('/'))) {
            return redirect($request->redirect);
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
