<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.fgpass');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $customMessage = 'We have emailed your password reset link!';

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => $customMessage])
                    : back()->withErrors(['email' => __($status)]);
    }
}
