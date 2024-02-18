<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin(){
        return view('auth.login');
    }

    public function showRegister(){
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $this->middleware('web');

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Retrieve the authenticated user
            $request->session()->regenerate();
            return redirect()->intended(route('index'));
        } else {
            return back()->withErrors(['email' => 'Verifique as suas credenciais'])->onlyInput('email');
        }
    }


    public function logout(Request $request){
        Auth::logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        return redirect()->route('index');
    }

    public function register(Request $request){

        $request->validate([
            'name'=>['required','string','max:50'],
            'email'=>['required','email','unique:users'],
            'password'=>['required','string','min:8'],
            'password_confirmation'=>['required','string','min:8','same:password']
        ]);

        $user = (new \App\Http\Controllers\UserController)->controller_create($request);

        Auth::login($user);

        return redirect()->route('index');
    }
}
