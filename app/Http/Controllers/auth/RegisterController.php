<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm(){
        return view('auth.register');
    }

    public function register(Request $request){

        $request->validate([
            'name'=>['required','string','max:50'],
            'email'=>['required','email','unique:users'],
            'password'=>['required','string','min:8'],
            'password_confirmation'=>['required','string','min:8','same:password']
        ]);

        $user = (new UserController)->controller_create($request);

        Auth::login($user);

        return redirect()->route('index');
    }

}
