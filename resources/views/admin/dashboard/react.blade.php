<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SkillNest Admin</title>
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
    @vite(['resources/panels/src/admin/main.jsx'])
</head>
<body>
    <div id="admin-root"></div>
    <script>
        window.__USER__ = @json($userData);
        window.__ADMIN_USER__ = window.__USER__;
    </script>
</body>
</html>
