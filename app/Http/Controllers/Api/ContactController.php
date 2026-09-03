<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $data = $request->only(['name', 'email', 'subject', 'message']);

        $sent = MailService::sendTemplate(
            'contact_form',
            config('mail.from.address'),
            $data,
            ['address' => $data['email'], 'name' => $data['name']]
        );

        if ($sent) {
            return response()->json([
                'status' => true,
                'message' => 'Your message has been sent successfully',
            ]);
        }

        // Fallback: plain mail when no contact template is configured.
        try {
            Mail::raw(
                "Name: {$data['name']}\nEmail: {$data['email']}\nSubject: {$data['subject']}\n\nMessage:\n{$data['message']}",
                function ($message) use ($data) {
                    $message->to(config('mail.from.address'))
                        ->subject("Contact Form: {$data['subject']}")
                        ->replyTo($data['email'], $data['name']);
                }
            );

            return response()->json([
                'status' => true,
                'message' => 'Your message has been sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send message. Please try again later.',
            ], 500);
        }
    }
}
