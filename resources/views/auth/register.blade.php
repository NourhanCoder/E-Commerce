@extends('website.layouts.master')

@section('title', 'Create Account')

@section('content')

<div class="container mt-5" style="padding-top: 70px;">
    <div class="row justify-content-center">
        <div class="col-md-6">


            <h2 class="text-center mb-4">إنشاء حساب جديد</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">الاسم كامل</label>
                    <input type="text" name="name" id="name" class="form-control" 
                           value="{{ old('name') }}" required autofocus>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" class="form-control" style="direction: ltr; text-align: left;"
                           value="{{ old('email') }}" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 alert alert-danger" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" name="password" id="password" class="form-control" style="direction: ltr; text-align: left;"
                           required autocomplete="new-password">
                           <x-input-error :messages="$errors->get('password')" class="mt-2 alert alert-danger" />
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" style="direction: ltr; text-align: left;"
                           class="form-control" required>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 alert alert-danger" />
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-dark">تسجيل</button>
                </div>

                <div class="text-center">
                    <a class="text-muted" href="{{ route('login') }}">
                        هل لديك حساب بالفعل؟ تسجيل الدخول
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<body>
    <script>
    function autoDirection(input) {
        input.addEventListener('input', function () {
            const value = input.value.trim();
            if (value.length === 0) {
                input.style.direction = 'rtl';
                return;
            }

            const firstChar = value.charAt(0);
            const isArabic = /[\u0600-\u06FF]/.test(firstChar); // range of Arabic letters

            input.style.direction = isArabic ? 'rtl' : 'ltr';
            input.style.textAlign = isArabic ? 'right' : 'left';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[type="text"], input[type="email"], textarea').forEach(function (input) {
            autoDirection(input);
        });
    });
</script>
</body>

@endsection