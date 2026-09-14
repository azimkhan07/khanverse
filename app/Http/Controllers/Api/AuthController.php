<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Buyer;
use App\Models\BuyerProfile;
use App\Models\LoginHistory;
use App\Models\Seller;
use App\Models\SellerCategoryDetail;
use App\Models\SellerProfile;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserDevice;
use App\Support\UserAgentParser;
use App\Services\MailService;
use App\Services\NotificationService;
use App\Services\OtpService;
use App\Services\PdfService;
use App\Services\KycVerificationService;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        $this->recordLoginSession($user, $request);

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

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $role = $request->role ?? 'user';

        $user = User::create([
            'name' => $request->name,
            'username' => Str::lower(trim((string) ($request->username ?: $this->generateUsername($request->email, $request->name)))),
            'phone' => $request->phone,
            'email' => Str::lower(trim((string) $request->email)),
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        if ($role === 'seller' || $role === 'buyer') {
            $this->createRoleRecord($user, $role, $request);
        }

        event(new Registered($user));

        Auth::login($user);

        $this->recordLoginSession($user, $request);

        NotificationService::send(
            $user->id,
            'Welcome to SkillNest',
            'Your account was created successfully. Complete your profile and choose whether you want to buy or sell services.',
            'auth',
            '/profile',
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
        $user = Auth::user();
        if ($user) {
            LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first()
                ?->update(['logout_at' => now()]);
        }

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
            'user' => ProfileService::data($user),
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

    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'No account found for this email.',
            ], 422);
        }

        $debug = config('app.debug', false);
        $otp = OtpService::send($user->id, $user->email, 'email', $debug);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email.',
            'debug_otp' => $debug ? $otp : null,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'No account found for this email.',
            ], 422);
        }

        if (! OtpService::verify($user->email, $request->otp)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Email verified successfully.',
        ]);
    }

    public function becomeSeller(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'seller') {
            return response()->json([
                'status' => true,
                'message' => 'You are already a seller.',
                'redirect' => '/seller',
            ]);
        }

        $validated = $request->validate([
            'privacy_policy' => ['required', 'accepted'],
            'full_name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'experience_level' => ['nullable', 'in:junior,mid,senior'],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'available_for_work' => ['nullable', 'boolean'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'category_id' => ['nullable', 'integer'],
            'category_details' => ['nullable', 'array'],
            'aadhaar_number' => ['nullable', 'string', 'max:20', $this->kycAadhaarRule()],
            'pan_number' => ['nullable', 'string', 'max:20', $this->kycPanRule()],
            'aadhaar_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'pan_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $aadhaarDoc = $request->hasFile('aadhaar_document') ? $request->file('aadhaar_document')->store('seller-kyc', 'public') : null;
        $panDoc = $request->hasFile('pan_document') ? $request->file('pan_document')->store('seller-kyc', 'public') : null;
        $previous = $user->seller;

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
                'aadhaar_number' => $validated['aadhaar_number'] ?? $previous?->aadhaar_number ?? null,
                'pan_number' => $validated['pan_number'] ?? $previous?->pan_number ?? null,
                'aadhaar_document' => $aadhaarDoc ?? $previous?->aadhaar_document ?? null,
                'pan_document' => $panDoc ?? $previous?->pan_document ?? null,
                'kyc_status' => ($aadhaarDoc || $panDoc) ? 'submitted' : ($previous?->kyc_status ?? 'pending'),
            ]
        );

        $this->applySellerGeo($seller, $validated, 'registration');

        if (! empty($validated['category_details']) || ! empty($validated['category_id'])) {
            SellerCategoryDetail::updateOrCreate(
                ['seller_id' => $seller->id],
                [
                    'seller_id' => $seller->id,
                    'category_id' => $validated['category_id'] ?? $seller->categoryDetail?->category_id ?? null,
                    'data' => $validated['category_details'] ?? $seller->categoryDetail?->data ?? null,
                ]
            );
        }

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

        $this->sendRoleWelcomeEmail($user, 'seller');

        return response()->json([
            'status' => true,
            'message' => 'You are now a seller.',
            'redirect' => '/seller',
            'profile' => ProfileService::data($user->fresh()),
        ]);
    }

    public function becomeBuyer(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'buyer') {
            return response()->json([
                'status' => true,
                'message' => 'You are already a buyer.',
                'redirect' => '/buyer',
            ]);
        }

        $validated = $request->validate([
            'privacy_policy' => ['required', 'accepted'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'is_consultancy' => ['nullable', 'boolean'],
            'verification_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $verificationDoc = $request->hasFile('verification_document') ? $request->file('verification_document')->store('buyer-verification', 'public') : null;
        $previous = $user->buyer;

        $buyer = Buyer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'full_name' => $validated['full_name'] ?? $user->name,
                'company_name' => $validated['company_name'] ?? null,
                'country' => $validated['country'] ?? null,
                'city' => $validated['city'] ?? null,
                'is_consultancy' => $request->has('is_consultancy')
                    ? $request->boolean('is_consultancy')
                    : (bool) ($previous?->is_consultancy ?? false),
                'verification_document' => $verificationDoc ?? $previous?->verification_document ?? null,
                'verification_status' => $verificationDoc ? 'submitted' : ($previous?->verification_status ?? 'pending'),
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
            '/buyer',
        );

        $this->sendRoleWelcomeEmail($user, 'buyer');

        return response()->json([
            'status' => true,
            'message' => 'You are now a buyer.',
            'redirect' => '/buyer',
            'profile' => ProfileService::data($user->fresh()),
        ]);
    }

    protected function sendRoleWelcomeEmail(User $user, string $role): void
    {
        $key = $role === 'seller' ? 'role_welcome_seller' : 'role_welcome_buyer';

        try {
            $pdfPath = PdfService::privacyPolicyFile([
                'user_name' => $user->name,
                'role' => ucfirst($role),
            ]);

            MailService::sendTemplate(
                $key,
                $user->email,
                [
                    'name' => $user->name,
                    'app_name' => config('app.name', 'SkillNest'),
                    'privacy_url' => url('/privacy-policy'),
                    'dashboard_url' => url('/' . $role),
                ],
                [],
                [[
                    'path' => $pdfPath,
                    'as' => 'privacy-policy-' . $role . '.pdf',
                    'mime' => 'application/pdf',
                ]]
            );

            @unlink($pdfPath);
        } catch (\Throwable $e) {
            report($e);
        }
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
            'complete' => ProfileService::isComplete($user),
            'missing' => ProfileService::completeness($user)['missing'],
            'avatar' => $user->display_image,
            'display_name' => $user->display_name,
            'record' => $record,
            'profile' => $profile,
        ]);
    }

    private function recordLoginSession(User $user, Request $request): void
    {
        try {
            $ip       = $request->ip();
            $userAgent = $request->userAgent();

            $info = UserAgentParser::parse($userAgent);

            $this->syncSellerLocation($user, $request, 'login');

            LoginHistory::create([
                'user_id'       => $user->id,
                'ip_address'    => $ip,
                'latitude'      => $this->requestLat($request),
                'longitude'     => $this->requestLng($request),
                'user_agent'    => $userAgent,
                'browser'       => $info['browser'],
                'device'        => $info['device'],
                'platform'      => $info['platform'],
                'login_at'      => now(),
                'is_successful' => true,
            ]);

            $friendlyName = trim(($info['browser'] ?? 'Unknown') . ' on ' . ($info['platform'] ?? 'Unknown') . ' - ' . ($info['device'] ?? 'Device'));

            $device = UserDevice::where('user_id', $user->id)
                ->where('ip_address', $ip)
                ->where('user_agent', $userAgent)
                ->first();

            if ($device) {
                $device->update([
                    'browser'           => $info['browser'],
                    'platform'          => $info['platform'],
                    'is_current_device' => true,
                    'last_activity'     => now(),
                ]);
            } else {
                UserDevice::where('user_id', $user->id)->update(['is_current_device' => false]);
                UserDevice::create([
                    'user_id'           => $user->id,
                    'device_name'       => $friendlyName,
                    'browser'           => $info['browser'],
                    'platform'          => $info['platform'],
                    'ip_address'        => $ip,
                    'user_agent'        => $userAgent,
                    'is_current_device' => true,
                    'last_activity'     => now(),
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
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

    private function kycAadhaarRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (empty($value)) {
                return;
            }
            $result = app(KycVerificationService::class)->aadhaar($value);
            if (! $result['valid']) {
                $fail($result['message'] ?? 'Aadhaar number is not valid.');
            }
        };
    }

    private function kycPanRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (empty($value)) {
                return;
            }
            $result = app(KycVerificationService::class)->pan($value);
            if (! $result['valid']) {
                $fail($result['message'] ?? 'PAN is not valid.');
            }
        };
    }

    private function requestLat(Request $request)
    {
        $lat = $request->input('latitude');
        return is_numeric($lat) && $lat >= -90 && $lat <= 90 ? (float)$lat : null;
    }

    private function requestLng(Request $request)
    {
        $lng = $request->input('longitude');
        return is_numeric($lng) && $lng >= -180 && $lng <= 180 ? (float)$lng : null;
    }

    private function applySellerGeo(Seller $seller, array $validated, string $source): void
    {
        $lat = $validated['latitude'] ?? null;
        $lng = $validated['longitude'] ?? null;
        if (is_numeric($lat) && is_numeric($lng)) {
            $seller->update([
                'latitude' => (float)$lat,
                'longitude' => (float)$lng,
                'location_source' => $source,
                'location_updated_at' => now(),
            ]);
        }
    }

    private function syncSellerLocation(User $user, Request $request, string $source): void
    {
        try {
            $lat = $this->requestLat($request);
            $lng = $this->requestLng($request);
            if (is_numeric($lat) && is_numeric($lng) && $user->seller) {
                $user->seller->update([
                    'latitude' => (float)$lat,
                    'longitude' => (float)$lng,
                    'location_source' => $source,
                    'location_updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
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
            'seller' => '/',
            'buyer' => '/',
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
