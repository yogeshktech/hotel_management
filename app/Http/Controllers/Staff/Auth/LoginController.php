<?php

namespace App\Http\Controllers\Staff\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('guest:staff')->except('logout');
    }

    public function showLoginForm()
    {
        return view('staff.auth.login');
    }

    protected function guard()
    {
        return auth()->guard('staff');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->update(['last_login_at' => now()]);

        if ($user->hasRole('vendor')) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }
}
