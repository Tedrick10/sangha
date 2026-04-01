<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sangha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentLoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('sangha.dashboard');
        }
        return redirect()->route('website.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $sangha = Sangha::where('username', $credentials['username'])->first();
        if ($sangha && filled($sangha->password) && Hash::check($credentials['password'], $sangha->password) && (! $sangha->approved || ! $sangha->is_active)) {
            $message = 'Your account is pending admin approval.';
            if (filled($sangha->rejection_reason)) {
                $message = 'Your account was rejected: ' . $sangha->rejection_reason;
            } elseif (! $sangha->is_active) {
                $message = 'Your account is currently inactive. Please contact admin.';
            }

            return redirect()->route('website.login')
                ->withErrors(['username' => $message])
                ->with('login_form', 'sangha')
                ->withInput($request->only('username'));
        }

        if (Auth::guard('student')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'approved' => true,
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('sangha.dashboard'));
        }

        return redirect()->route('website.login')
            ->withErrors(['username' => __('auth.failed')])
            ->with('login_form', 'sangha')
            ->withInput($request->only('username'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('website.login');
    }
}
