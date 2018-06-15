<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{

    public function showLogin()
    {
        if (Auth::check()) {
            Auth::logout();

            session()->forget('identity');
            session()->forget('user_id');
        }

        return view('login');
    }

    public function doLogin(Request $request)
    {
        $rules = array(
            'username' => 'required',
            'password' => 'required'
        );

        $messages = array(
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.'
        );

        $this->validate($request, $rules, $messages);

        $userdata = array(
            'name' => $request->input('username'),
            'password' => $request->input('password')
        );

        if (Auth::attempt($userdata)) {
            session(['identity' => Auth::user()->permission_level_id]);
            session(['user_id' => Auth::user()->id]);
            return redirect()->intended(asset('/'));
        } else {
            return redirect(asset('/login'))->with('login_fail_message', 'Login failed. You have the wrong username or password.');
        }
    }

    public function doLogout()
    {
        Auth::logout();

        session()->forget('identity');
        session()->forget('user_id');

        return redirect(asset('/login'));
    }
}
