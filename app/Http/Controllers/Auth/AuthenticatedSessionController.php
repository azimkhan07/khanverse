<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        if ($user->role == 'admin') {
            $redirect = route('admin.dashboard');
        } elseif ($user->role == 'seller') {
            $redirect = '/seller';
        } elseif ($user->role == 'buyer') {
            $redirect = '/buyer';
        } else {
            Auth::logout();
            return response()->json(['status' => false, 'message' => 'Invalid User Role'], 403);
        }

        // Track login history
        $this->trackLogin($user, $request);

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Login Successful',
                'redirect' => $redirect,
            ]);
        }

        return redirect()->intended($redirect);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user) {
            LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->update(['logout_at' => now()]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function trackLogin($user, $request)
    {
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
