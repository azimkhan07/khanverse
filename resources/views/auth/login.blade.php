{{-- resources/views/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Khanverse Login</title>

    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/master.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="auth-page">

    {{-- Animated Background --}}
    <div class="auth-background">

        <div class="gradient-circle circle-1"></div>
        <div class="gradient-circle circle-2"></div>
        <div class="gradient-circle circle-3"></div>

    </div>

    <div class="auth-container">

        {{-- LEFT SIDE --}}
        <div class="auth-left">

            <div class="brand-content">

                <div class="logo-box">

                    <img src="{{ asset('admin/assets/images/logo.png') }}" alt="Logo">

                </div>

                <span class="mini-tag">
                    FREELANCER MARKETPLACE
                </span>

                <h1>
                    Hire Experts.<br>
                    Grow Faster.
                </h1>

                <p>
                    Connect with talented freelancers, manage projects,
                    and build your digital business with Khanverse.
                </p>

                <div class="feature-list">

                    <div class="feature-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Trusted Freelancers
                        </span>

                    </div>

                    <div class="feature-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Secure Payments
                        </span>

                    </div>

                    <div class="feature-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Real Time Collaboration
                        </span>

                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="auth-right">

            <div class="login-card">

                <div class="login-header">

                    <h2>
                        Welcome Back
                    </h2>

                    <p>
                        Login to continue your journey
                    </p>

                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">

                        {{ $errors->first() }}

                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">

                    @csrf

                    <div class="form-group">

                        <label>
                            Email Address
                        </label>

                        <div class="input-box">

                            <i class="fa-regular fa-envelope"></i>

                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="Enter your email" required>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>
                            Password
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-lock"></i>

                            <input type="password" name="password" placeholder="Enter your password" required>
                            <input type="hidden" name="csrf-token" id="csrf-token" value="{{ csrf_token() }}">
                        </div>

                    </div>

                    <div class="remember-row">

                        <div class="remember-box">

                            <input type="checkbox" name="remember" id="remember">

                            <label for="remember">
                                Remember Me
                            </label>

                        </div>

                        <a href="#">
                            Forgot Password?
                        </a>

                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">

                        <span id="btnText">
                            Login Now
                        </span>

                        <span id="btnLoader" style="display:none;">

                            <i class="fa fa-spinner fa-spin"></i>
                            Please Wait...

                        </span>

                    </button>

                </form>

                <div class="auth-footer">

                    <span>
                        New to Khanverse?
                    </span>

                    <a href="#">
                        Create Account
                    </a>

                </div>

            </div>

        </div>

    </div>
    {{-- JQUERY --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {

            /*  =====================================  CSRF TOKEN SETUP ===================================== */

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            /* ===================================== AUTO FILL REMEMBER DATA  ===================================== */

            let rememberEmail = localStorage.getItem('remember_email');

            let rememberPassword = localStorage.getItem('remember_password');

            if (rememberEmail !== null && rememberPassword !== null) {

                $('#email').val(rememberEmail);

                $('#password').val(rememberPassword);

                $('#remember').prop('checked', true);

            }

            /*
            =====================================
            ENTER PRESS LOGIN
            =====================================
            */

            $('#password').keypress(function(e) {

                if (e.which == 13) {

                    $('#loginForm').submit();

                }

            });

            /*
            =====================================
            LOGIN FORM SUBMIT
            =====================================
            */

            $('#loginForm').submit(function(e) {

                e.preventDefault();

                /*
                =====================================
                BUTTON LOADING STATE
                =====================================
                */

                $('#loginBtn').prop('disabled', true);

                $('#btnText').hide();

                $('#btnLoader').show();

                /*
                =====================================
                REMOVE OLD ALERT
                =====================================
                */

                $('.custom-alert').remove();

                /*
                =====================================
                FORM DATA
                =====================================
                */

                let formData = $(this).serialize();

                /*
                =====================================
                AJAX LOGIN REQUEST
                =====================================
                */

                $.ajax({

                    url: "{{ route('login') }}",

                    type: "POST",

                    data: formData,

                    success: function(response) {

                        /*
                        =====================================
                        REMEMBER ME SAVE
                        =====================================
                        */

                        if ($('#remember').is(':checked')) {

                            localStorage.setItem(
                                'remember_email',
                                $('#email').val()
                            );

                            localStorage.setItem(
                                'remember_password',
                                $('#password').val()
                            );

                        } else {

                            localStorage.removeItem('remember_email');

                            localStorage.removeItem('remember_password');

                        }

                        /*
                        =====================================
                        SUCCESS MESSAGE
                        =====================================
                        */

                        $('.login-card').prepend(

                            `
                    <div class="alert alert-success custom-alert">

                        Login Successful...
                        Redirecting...

                    </div>
                    `
                        );

                        /*
                        =====================================
                        REDIRECT
                        =====================================
                        */

                        setTimeout(function() {

                            window.location.href = response.redirect;

                        }, 1000);

                    },

                    error: function(xhr) {

                        /*
                        =====================================
                        BUTTON RESET
                        =====================================
                        */

                        $('#loginBtn').prop('disabled', false);

                        $('#btnLoader').hide();

                        $('#btnText').show();

                        /*
                        =====================================
                        DEFAULT ERROR
                        =====================================
                        */

                        let errorMessage = 'Invalid Login Credentials';

                        /*
                        =====================================
                        VALIDATION ERRORS
                        =====================================
                        */

                        if (xhr.responseJSON) {

                            if (xhr.responseJSON.message) {

                                errorMessage = xhr.responseJSON.message;

                            }

                            /*
                            =====================================
                            LARAVEL VALIDATION ERROR
                            =====================================
                            */

                            if (xhr.responseJSON.errors) {

                                let firstError = Object.values(
                                    xhr.responseJSON.errors
                                )[0];

                                errorMessage = firstError[0];

                            }

                        }

                        /*
                        =====================================
                        SHOW ERROR ALERT
                        =====================================
                        */

                        $('.login-card').prepend(

                            `
                    <div class="alert alert-danger custom-alert">

                        ${errorMessage}

                    </div>
                    `
                        );

                    }

                });

            });

        });
    </script>

</body>

</html>
