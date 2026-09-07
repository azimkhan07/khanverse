<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('auth', 'forgot.title', 'Forgot Password') }} — {{ config('app.name', 'KhanVerse') }}</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/master.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="auth-page">

    <div class="auth-grid" id="authGrid"></div>

    <div class="auth-card">

        <div class="auth-card-header">
            <div class="logo-box">
                @if(setting('auth', 'auth.logo'))
                    <img src="{{ asset('storage/' . setting('auth', 'auth.logo')) }}" alt="{{ setting('auth', 'auth.name', 'KhanVerse') }}">
                @else
                    <span>{{ setting('auth', 'auth.name', 'KhanVerse') }}</span>
                @endif
            </div>
            <h2>{{ setting('auth', 'forgot.heading', 'Forgot Password?') }}</h2>
            <p>{{ setting('auth', 'forgot.subheading', 'No problem. Just let us know your email address and we will email you a password reset link.') }}</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-box">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <button type="submit" class="auth-btn">
                <span>{{ setting('auth', 'forgot.button', 'Email Password Reset Link') }}</span>
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
