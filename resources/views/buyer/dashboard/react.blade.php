<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buyer - KhanVerse</title>
    <script>
        (function () {
            try {
                var u = {{ auth()->check() ? auth()->user()->id : 'null' }};
                var k = u ? 'khanverse-theme-buyer-' + u : 'khanverse-theme-buyer';
                if (localStorage.getItem(k) === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) { /* ignore */ }
        })();
    </script>
    @php
        $user = auth()->user();
        $userData = $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'avatar' => $user->profile_photo_path ?? null,
        ] : null;
    @endphp
    @vite(['resources/panels/src/buyer/main.jsx'])
</head>
<body>
    <div id="buyer-root"></div>
    <script>
        window.__USER__ = @json($userData);
        window.__BUYER_USER__ = window.__USER__;
    </script>
</body>
</html>
