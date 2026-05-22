<!DOCTYPE html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('partials.header')
{{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

<body>

    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>

    <div id="pcoded" class="pcoded">

        <div class="pcoded-overlay-box"></div>

        <div class="pcoded-container navbar-wrapper">

            {{-- Sidebar --}}
            @include('layouts.sidebar')

            <div class="pcoded-main-container">

                <div class="pcoded-wrapper">

                    {{-- Top Navbar --}}
                    @include('layouts.topNav')

                    <div class="pcoded-content">

                        <div class="page-inner">
                            @yield('content')
                        </div>

                    </div>

                    {{-- Footer --}}
                    @include('layouts.footer')

                </div>
            </div>
        </div>
    </div>
    <style>
        .modal {
            z-index: 99999 !important;
        }

        .modal-backdrop {
            z-index: 9999 !important;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };
    </script>
    @stack('js')
</body>

</html>
