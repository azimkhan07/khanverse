<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('auth', 'reset.title', 'Reset Password') }} — {{ config('app.name', 'SkillNest') }}</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/master.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="auth-page">

    <div class="auth-grid" id="authGrid"></div>

    <div class="auth-card">

        <div class="auth-card-header">
            <div class="logo-box">
                @if(setting('auth', 'auth.logo'))
                    <img src="{{ asset('storage/' . setting('auth', 'auth.logo')) }}" alt="{{ setting('auth', 'auth.name', 'SkillNest') }}">
                @else
                    <span>{{ setting('auth', 'auth.name', 'SkillNest') }}</span>
                @endif
            </div>
            <h2>{{ setting('auth', 'reset.heading', 'Reset Password') }}</h2>
            <p>{{ setting('auth', 'reset.subheading', 'Choose a new password for your account.') }}</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-box">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password_confirmation" placeholder="Confirm new password" required>
                </div>
            </div>

            <button type="submit" class="auth-btn">
                <span>{{ setting('auth', 'reset.button', 'Reset Password') }}</span>
            </button>
        </form>

        <div class="auth-footer auth-form">
            <span>Remember your password?</span>
            <a href="{{ route('login') }}">Back to Login</a>
        </div>

    </div>

    <script src="{{ asset('admin/assets/js/auth.js') }}"></script>

</body>
</html>
