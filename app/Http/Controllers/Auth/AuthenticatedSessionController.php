<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        /*
        =====================================
        AUTHENTICATE USER
        =====================================
        */

        $request->authenticate();

        /*
        =====================================
        REGENERATE SESSION
        =====================================
        */

        $request->session()->regenerate();

        /*
        =====================================
        GET LOGIN USER
        =====================================
        */

        $user = auth()->user();

        /*
        =====================================
        ROLE BASED REDIRECT
        =====================================
        */

        if ($user->role == 'admin') {

            $redirect = route('admin.dashboard');
        } elseif ($user->role == 'seller') {

            $redirect = route('seller.dashboard');
        } elseif ($user->role == 'buyer') {

            $redirect = route('buyer.dashboard');
        } else {

            /*
            =====================================
            UNKNOWN ROLE
            =====================================
            */

            Auth::logout();

            return response()->json([

                'status' => false,

                'message' => 'Invalid User Role'

            ], 403);
        }

        /*
        =====================================
        AJAX RESPONSE
        =====================================
        */

        if ($request->ajax()) {

            return response()->json([

                'status' => true,

                'message' => 'Login Successful',

                'redirect' => $redirect

            ]);
        }

        /*
        =====================================
        NORMAL RESPONSE
        =====================================
        */

        return redirect()->intended($redirect);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
