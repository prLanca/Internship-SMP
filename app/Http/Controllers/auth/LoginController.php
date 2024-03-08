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
    public function showLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->middleware('web');

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        // Caso o e-mail não exista, exibir mensagem de erro personalizada
        if (!User::where('email', $credentials['email'])->exists()) {
            return redirect()->route('login')->withErrors([
                'email' => 'E-mail não registado.',
            ]);
        }

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

}
