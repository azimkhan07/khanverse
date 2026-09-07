<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('auth', 'verify.title', 'Verify Email') }} — {{ config('app.name', 'SkillNest') }}</title>
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
            <h2>{{ setting('auth', 'verify.heading', 'Verify Your Email') }}</h2>
            <p>{{ setting('auth', 'verify.subheading', 'Thanks for signing up! Please verify your email address by clicking on the link we just emailed to you.') }}</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">A new verification link has been sent to the email address you provided during registration.</div>
        @endif

        <div class="auth-form" style="display:flex; flex-direction:column; gap:12px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="auth-btn">
                    <span>{{ setting('auth', 'verify.button', 'Resend Verification Email') }}</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="auth-social-btn" style="border:1px solid rgba(0,0,0,0.10); background:#fff;">
                    <span style="color:#757575;">Log Out</span>
                </button>
            </form>
        </div>

    </div>

    <script src="{{ asset('admin/assets/js/auth.js') }}"></script>

</body>
</html>
