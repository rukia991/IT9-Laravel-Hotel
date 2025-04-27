<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
{
    if (Auth::attempt($request->only('email', 'password'))) {
        $user = auth()->user();

        activity()->causedBy($user)->log('User logged into the portal'); // Log activity message

        // Role-based redirection
        switch (strtolower($user->role)) {
            case 'super':
                return redirect()->route('dashboard.index')->with('success', 'Welcome '.$user->name);
            case 'customer':
                return redirect()->route('customer.index')->with('success', 'Welcome '.$user->name);
            case 'receptionist':
                return redirect()->route('receptionist.index')->with('success', 'Welcome '.$user->name);
            case 'manager':
                return redirect()->route('manager.index')->with('success', 'Welcome '.$user->name);
            default:
                Auth::logout();
                return redirect()->route('login.index')->with('failed', 'Your role is not authorized to access this portal.');
        }
    }

    return redirect()->route('login.index')->with('failed', 'Incorrect email / password');
}

public function logout()
{
    $name = auth()->user()->name;
    Auth::logout();

    return redirect()->route('login.index')->with('success', 'Logout success, goodbye ' . $name);
}

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.index')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    
        return redirect()->route('login.index')->with('success', 'Registration successful! Please log in.');
    }
}
