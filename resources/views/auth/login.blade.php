<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('auth', 'login.title', 'KhanVerse Login') }}</title>
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
            <h2>{{ setting('auth', 'login.heading', 'Welcome Back') }}</h2>
            <p>{{ setting('auth', 'login.subheading', 'Login to continue your journey') }}</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm" class="auth-form">
            @csrf

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-box">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="remember-row">
                <div class="remember-box">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">{{ setting('auth', 'login.remember', 'Remember Me') }}</label>
                </div>
                <a href="{{ route('password.request') }}">{{ setting('auth', 'login.forgot_link', 'Forgot Password?') }}</a>
            </div>

            <div id="savedBadge" class="auth-saved-badge">
                <i class="fa-solid fa-circle-check"></i>
                <span>Saved credentials loaded</span>
            </div>

            <button type="submit" class="auth-btn" id="loginBtn">
                <span id="btnText">{{ setting('auth', 'login.button', 'Login Now') }}</span>
                <span id="btnLoader" style="display:none;">
                    <i class="fa fa-spinner fa-spin"></i> Please Wait...
                </span>
            </button>
        </form>

        <div class="auth-divider">
            <span>or continue with</span>
        </div>

        <a href="{{ route('google.redirect') }}" class="auth-social-btn">
            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            <span>Continue with Google</span>
        </a>

        <div class="auth-footer auth-form">
            <span style="font-size: 12px; margin-top: 2px; color: #888a8a;">{{ setting('auth', 'login.new_here', 'New to KhanVerse?') }}</span>
            <a style="font-size: 13px;" href="{{ route('register') }}">{{ setting('auth', 'login.create_account', 'Create Account') }}</a>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin/assets/js/auth.js') }}"></script>
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            var rememberEmail = localStorage.getItem('remember_email');
            var rememberPasswordEnc = localStorage.getItem('remember_password_enc');
            if (rememberEmail !== null && rememberPasswordEnc !== null) {
                $('#email').val(rememberEmail);
                $('#remember').prop('checked', true);
                $('#savedBadge').addClass('visible');
                // Decrypt password via server
                $.post('{{ route("decrypt.value") }}', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    value: rememberPasswordEnc
                }, function (res) {
                    if (res.decrypted) $('#password').val(res.decrypted);
                });
            }

            $('#remember').on('change', function () {
                if (!$(this).is(':checked')) {
                    localStorage.removeItem('remember_email');
                    localStorage.removeItem('remember_password_enc');
                    $('#savedBadge').removeClass('visible');
                }
            });

            $('#password').keypress(function(e) {
                if (e.which == 13) { $('#loginForm').submit(); }
            });

            $('#loginForm').submit(function(e) {
                e.preventDefault();

                $('#loginBtn').prop('disabled', true);
                $('#btnText').hide();
                $('#btnLoader').show();
                $('.custom-alert').remove();

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('login') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if ($('#remember').is(':checked')) {
                            localStorage.setItem('remember_email', $('#email').val());
                            // Encrypt password before storing
                            $.post('{{ route("encrypt.value") }}', {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                value: $('#password').val()
                            }, function (res) {
                                localStorage.setItem('remember_password_enc', res.encrypted);
                            });
                        } else {
                            localStorage.removeItem('remember_email');
                            localStorage.removeItem('remember_password_enc');
                        }

                        $('.auth-card').prepend('<div class="alert alert-success custom-alert">Login Successful... Redirecting...</div>');
                        setTimeout(function() { window.location.href = response.redirect; }, 1000);
                    },
                    error: function(xhr) {
                        $('#loginBtn').prop('disabled', false);
                        $('#btnLoader').hide();
                        $('#btnText').show();

                        var errorMessage = 'Invalid Login Credentials';
                        var firstError = null;
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) { errorMessage = xhr.responseJSON.message; }
                            if (xhr.responseJSON.errors) {
                                var errorValues = Object.values(xhr.responseJSON.errors);
                                if (errorValues.length) { firstError = errorValues[0][0]; }
                            }
                        }
                        if (firstError) { errorMessage = firstError; }
                        $('<div class="alert alert-danger custom-alert"></div>').text(errorMessage).prependTo('.auth-card');
                    }
                });
            });
        });
    </script>

</body>
</html>
