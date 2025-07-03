@extends('website.layouts.master')

@section('title', 'تسجيل الدخول')

@section('content')

<div class="container mt-5" style="padding-top: 70px;">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h2 class="text-center mb-4">تسجيل الدخول</h2>
             <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" class="form-control" style="direction: ltr; text-align: left;"
                           value="{{ old('email') }}" required autofocus>
                           <x-input-error :messages="$errors->get('email')" class="mt-2 alert alert-danger" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" name="password" id="password" class="form-control" style="direction: ltr; text-align: left;"
                           required autocomplete="current-password">
                           <x-input-error :messages="$errors->get('password')" class="mt-2 alert alert-danger"/>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">تذكرني</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-dark">تسجيل الدخول</button>
                </div>

                @if (Route::has('password.request'))
                    <div class="text-center">
                        <a class="text-muted" href="{{ route('password.request') }}">
                            هل نسيت كلمة المرور؟
                        </a>
                    </div>
                    <div class="text-center">
                        <a class="text-muted" href="{{ route('register') }}">
                              هل ليس لديك حساب؟ إنشاء حساب
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>



@endsection