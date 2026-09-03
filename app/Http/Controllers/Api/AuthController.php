<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Buyer;
use App\Models\BuyerProfile;
use App\Models\Seller;
use App\Models\SellerProfile;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'redirect' => $this->getRedirectUrl($user),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'in:buyer,seller'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bio' => ['nullable', 'string'],
            'skills' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'experience_level' => ['nullable', 'in:junior,mid,senior'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $role = $request->role ?? 'buyer';

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username ?: $this->generateUsername($request->email, $request->name),
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        $this->createRoleRecord($user, $role, $request);

        event(new Registered($user));

        Auth::login($user);

        NotificationService::send(
            $user->id,
            'Welcome to KhanVerse',
            'Your ' . $role . ' account was created successfully. Complete your profile to get started.',
            'auth',
            route($role . '.dashboard'),
        );

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'redirect' => $this->getRedirectUrl($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function verifyLock(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect password. Please try again.',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Unlocked.',
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['user' => null]);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
                'has_seller' => (bool) $user->seller,
                'has_buyer' => (bool) $user->buyer,
            ],
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'status' => $status === Password::RESET_LINK_SENT,
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Reset link sent to your email'
                : 'Unable to send reset link',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return response()->json([
            'status' => $status === Password::PASSWORD_RESET,
            'message' => $status === Password::PASSWORD_RESET
                ? 'Password reset successful'
                : 'Unable to reset password',
        ]);
    }

    public function authSettings(): JsonResponse
    {
        $settings = Setting::where('group', 'auth')->get();

        return response()->json($settings->pluck('value', 'key'));
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified',
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification link sent',
        ]);
    }

    public function becomeSeller(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'seller') {
            return response()->json([
                'status' => true,
                'message' => 'You are already a seller.',
                'redirect' => route('seller.dashboard'),
            ]);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'experience_level' => ['nullable', 'in:junior,mid,senior'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'available_for_work' => ['nullable', 'boolean'],
        ]);

        $seller = Seller::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'full_name' => $validated['full_name'] ?? $user->name,
                'bio' => $validated['bio'] ?? null,
                'skills' => $validated['skills'] ?? null,
                'country' => $validated['country'] ?? null,
                'city' => $validated['city'] ?? null,
                'hourly_rate' => $validated['hourly_rate'] ?? null,
                'experience_level' => $validated['experience_level'] ?? 'junior',
                'available_for_work' => $validated['available_for_work'] ?? true,
            ]
        );

        SellerProfile::updateOrCreate(
            ['seller_id' => $seller->id],
            [
                'seller_id' => $seller->id,
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'country_id' => $validated['country_id'] ?? null,
                'state_id' => $validated['state_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'address' => $validated['address'] ?? null,
            ]
        );

        $user->update([
            'role' => 'seller',
            'phone' => $request->has('phone') ? $request->phone : $user->phone,
        ]);

        NotificationService::send(
            $user->id,
            'You are now a Seller',
            'Welcome aboard! Start creating services to receive orders.',
            'role',
            route('seller.services.index'),
        );

        return response()->json([
            'status' => true,
            'message' => 'You are now a seller.',
            'redirect' => route('seller.dashboard'),
        ]);
    }

    public function becomeBuyer(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'buyer') {
            return response()->json([
                'status' => true,
                'message' => 'You are already a buyer.',
                'redirect' => route('buyer.dashboard'),
            ]);
        }

        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        $buyer = Buyer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'full_name' => $validated['full_name'] ?? $user->name,
                'company_name' => $validated['company_name'] ?? null,
                'country' => $validated['country'] ?? null,
                'city' => $validated['city'] ?? null,
            ]
        );

        BuyerProfile::updateOrCreate(
            ['buyer_id' => $buyer->id],
            [
                'buyer_id' => $buyer->id,
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'country_id' => $validated['country_id'] ?? null,
                'state_id' => $validated['state_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'address' => $validated['address'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ]
        );

        $user->update([
            'role' => 'buyer',
            'phone' => $request->has('phone') ? $request->phone : $user->phone,
        ]);

        NotificationService::send(
            $user->id,
            'You are now a Buyer',
            'Welcome aboard! Browse services and place your first order.',
            'role',
            route('buyer.dashboard'),
        );

        return response()->json([
            'status' => true,
            'message' => 'You are now a buyer.',
            'redirect' => route('buyer.dashboard'),
        ]);
    }

    public function currentRoleRecord(Request $request): JsonResponse
    {
        $user = $request->user();

        $record = null;
        $profile = null;

        if ($user->role === 'seller' && $user->seller) {
            $record = $user->seller;
            $profile = $user->seller->profile;
        } elseif ($user->role === 'buyer' && $user->buyer) {
            $record = $user->buyer;
            $profile = $user->buyer->profile;
        }

        return response()->json([
            'role' => $user->role,
            'has_seller' => (bool) $user->seller,
            'has_buyer' => (bool) $user->buyer,
            'record' => $record,
            'profile' => $profile,
        ]);
    }

    private function createRoleRecord(User $user, string $role, Request $request): void
    {
        if ($role === 'seller') {
            $seller = Seller::create([
                'user_id' => $user->id,
                'full_name' => $request->name,
                'bio' => $request->bio ?? null,
                'skills' => $request->skills ?? null,
                'country' => $request->country ?? null,
                'city' => $request->city ?? null,
                'hourly_rate' => $request->hourly_rate ?? null,
                'experience_level' => $request->experience_level ?? 'junior',
            ]);

            SellerProfile::updateOrCreate(
                ['seller_id' => $seller->id],
                [
                    'seller_id' => $seller->id,
                    'phone' => $request->phone ?? null,
                    'gender' => $request->gender ?? null,
                    'dob' => $request->dob ?? null,
                    'country_id' => $request->country_id ?? null,
                    'state_id' => $request->state_id ?? null,
                    'city_id' => $request->city_id ?? null,
                    'postal_code' => $request->postal_code ?? null,
                    'address' => $request->address ?? null,
                ]
            );
        } else {
            $buyer = Buyer::create([
                'user_id' => $user->id,
                'full_name' => $request->name,
                'company_name' => $request->company_name ?? null,
                'country' => $request->country ?? null,
                'city' => $request->city ?? null,
            ]);

            BuyerProfile::updateOrCreate(
                ['buyer_id' => $buyer->id],
                [
                    'buyer_id' => $buyer->id,
                    'phone' => $request->phone ?? null,
                    'gender' => $request->gender ?? null,
                    'dob' => $request->dob ?? null,
                    'country_id' => $request->country_id ?? null,
                    'state_id' => $request->state_id ?? null,
                    'city_id' => $request->city_id ?? null,
                    'postal_code' => $request->postal_code ?? null,
                    'address' => $request->address ?? null,
                ]
            );
        }
    }

    private function generateUsername(string $email, ?string $name = null): string
    {
        $base = $name ? Str::slug($name) : Str::before($email, '@');
        $base = $base ?: Str::before($email, '@');
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }
        return $username;
    }

    private function getRedirectUrl(User $user): string
    {
        $intended = session()->pull('url.intended');
        if ($intended && $this->intendedAllowedForRole($intended, $user->role)) {
            return $intended;
        }
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'seller' => route('seller.dashboard'),
            'buyer' => route('buyer.dashboard'),
            default => '/',
        };
    }

    private function intendedAllowedForRole(string $url, string $role): bool
    {
        foreach (['admin', 'seller', 'buyer'] as $p) {
            if (str_contains($url, '/'.$p.'/') || $url === '/'.$p) {
                return $role === $p;
            }
        }
        return false;
    }
}
