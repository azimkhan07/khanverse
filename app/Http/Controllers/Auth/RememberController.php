<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RememberController extends Controller
{
    public function encrypt(Request $request)
    {
        $request->validate(['value' => 'required|string']);
        return response()->json(['encrypted' => Crypt::encryptString($request->value)]);
    }

    public function decrypt(Request $request)
    {
        $request->validate(['value' => 'required|string']);
        try {
            return response()->json(['decrypted' => Crypt::decryptString($request->value)]);
        } catch (\Exception $e) {
            return response()->json(['decrypted' => ''], 400);
        }
    }
}
