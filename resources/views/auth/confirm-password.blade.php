<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('auth', 'confirm.title', 'Confirm Password') }} — {{ config('app.name', 'KhanVerse') }}</title>
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
            <h2>{{ setting('auth', 'confirm.heading', 'Confirm Password') }}</h2>
            <p>{{ setting('auth', 'confirm.subheading', 'This is a secure area. Please confirm your password before continuing.') }}</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="auth-btn">
                <span>{{ setting('auth', 'confirm.button', 'Confirm') }}</span>
            </button>
        </form>

    </div>

    <script src="{{ asset('admin/assets/js/auth.js') }}"></script>

</body>
</html>
