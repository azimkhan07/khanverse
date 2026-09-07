<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        $username = Str::slug($request->name) . '-' . Str::random(4);

        while (User::where('username', $username)->exists()) {
            $username = Str::slug($request->name) . '-' . Str::random(6);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'buyer',
            'is_verified' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Registration Successful',
                'redirect' => '/buyer',
            ]);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
