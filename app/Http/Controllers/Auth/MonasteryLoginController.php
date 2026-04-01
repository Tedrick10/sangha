<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Monastery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MonasteryLoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('monastery')->check()) {
            return redirect()->route('monastery.dashboard');
        }
        return redirect()->route('website.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $monastery = Monastery::where('username', $credentials['username'])->first();
        if ($monastery && filled($monastery->password) && Hash::check($credentials['password'], $monastery->password) && (! $monastery->approved || ! $monastery->is_active)) {
            $message = 'Your account is pending admin approval.';
            if (filled($monastery->rejection_reason)) {
                $message = 'Your account was rejected: ' . $monastery->rejection_reason;
            } elseif (! $monastery->is_active) {
                $message = 'Your account is currently inactive. Please contact admin.';
            }

            return redirect()->route('website.login')
                ->withErrors(['username' => $message])
                ->with('login_form', 'monastery')
                ->withInput($request->only('username'));
        }

        if (Auth::guard('monastery')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'approved' => true,
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('monastery.dashboard'));
        }

        return redirect()->route('website.login')
            ->withErrors(['username' => __('auth.failed')])
            ->with('login_form', 'monastery')
            ->withInput($request->only('username'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('monastery')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('website.login');
    }
}
