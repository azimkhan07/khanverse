<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->id]);
            } else {
                $username = Str::slug($googleUser->name) . '-' . Str::random(4);

                while (User::where('username', $username)->exists()) {
                    $username = Str::slug($googleUser->name) . '-' . Str::random(6);
                }

                $user = User::create([
                    'name' => $googleUser->name,
                    'username' => $username,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(),
                    'role' => 'buyer',
                    'is_verified' => true,
                ]);
            }

            Auth::login($user, true);

            // Track login
            $ua = $request->userAgent();
            $ip = $request->ip();

            LoginHistory::create([
                'user_id'      => $user->id,
                'ip_address'   => $ip,
                'user_agent'   => $ua,
                'browser'      => $this->detectBrowser($ua),
                'device'       => $this->detectDevice($ua),
                'platform'     => $this->detectPlatform($ua),
                'login_at'     => now(),
                'is_successful'=> true,
            ]);

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $ip,
            ]);

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role == 'seller') {
                return redirect('/seller');
            }

            return redirect('/buyer');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
        }
    }

    private function detectBrowser($ua)
    {
        if (stripos($ua, 'Firefox') !== false) return 'Firefox';
        if (stripos($ua, 'Edg') !== false) return 'Edge';
        if (stripos($ua, 'Chrome') !== false) return 'Chrome';
        if (stripos($ua, 'Safari') !== false) return 'Safari';
        if (stripos($ua, 'Opera') !== false || stripos($ua, 'OPR') !== false) return 'Opera';
        return 'Unknown';
    }

    private function detectDevice($ua)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $ua)) return 'Mobile';
        if (preg_match('/Tablet|iPad/i', $ua)) return 'Tablet';
        return 'Desktop';
    }

    private function detectPlatform($ua)
    {
        if (stripos($ua, 'Windows') !== false) return 'Windows';
        if (stripos($ua, 'Mac OS') !== false) return 'macOS';
        if (stripos($ua, 'Linux') !== false) return 'Linux';
        if (stripos($ua, 'Android') !== false) return 'Android';
        if (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) return 'iOS';
        return 'Unknown';
    }
}
