<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm(){
        return view('auth.register');
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Str::contains($value, ['Admin', 'Administrator'])) {
                        $fail($attribute . ' cannot contain "Admin" or "Administrator".');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users'), // Verifica se o e-mail é único na tabela de usuários
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails() || $request->input('name') === 'Administrator') {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        $user = (new UserController)->controller_create($request);

        Auth::login($user);

        return redirect()->route('index');
    }

}
