<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Enums\UserType;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();


        // return redirect()->intended(route('dashboard', absolute: false));

        // تحقق من حالة تفعيل المستخدم
        if (!auth()->user()->active) {
            auth()->logout();
            return back()->withErrors([
                'email' => 'حسابك ليس مفعل. برجاء محاولة التواصل مع الأدمن',
            ]);
        }
        // توجيه حسب النوع
        if (auth()->user()->type === UserType::Admin) {
            return redirect()->route('admin.panel');
        } elseif (auth()->user()->type === UserType::Customer) {
            return redirect()->route('home.page'); // أو أي صفحة رئيسية للمستخدم
        }

        auth()->logout(); // لو النوع غير معروف
        return redirect()->route('login')->withErrors(['email' => 'Unauthorized access']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
