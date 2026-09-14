<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KycVerificationService
{
    /**
     * Verifies an Aadhaar number locally (12-digit + Verhoeff checksum).
     * When a paid provider is configured, the remote verification is attempted
     * and silently falls back to the local result on any provider failure.
     */
    public function aadhaar(string $number): array
    {
        $cleaned = preg_replace('/\D/', '', (string) $number);
        if (! preg_match('/^\d{12}$/', $cleaned)) {
            return ['valid' => false, 'source' => 'format', 'message' => 'Aadhaar must be exactly 12 digits.'];
        }

        $valid = $this->verhoeff($cleaned);

        if ($valid && $this->providerEnabled()) {
            $remote = $this->providerCheck(['aadhaar' => $cleaned]);
            if ($remote !== null) {
                return $remote + ['source' => 'provider'];
            }
        }

        return [
            'valid' => $valid,
            'source' => 'local',
            'message' => $valid ? 'Aadhaar number is valid.' : 'Aadhaar number failed the checksum check.',
        ];
    }

    /**
     * Verifies a PAN number locally (ABCDE1234F format). Real correctness
     * requires a paid provider, which is attempted when configured.
     */
    public function pan(string $number): array
    {
        $normalized = strtoupper(trim((string) $number));
        if (! preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $normalized)) {
            return ['valid' => false, 'source' => 'format', 'message' => 'PAN must match the format ABCDE1234F.'];
        }

        if ($this->providerEnabled()) {
            $remote = $this->providerCheck(['pan' => $normalized]);
            if ($remote !== null) {
                return $remote + ['source' => 'provider'];
            }
        }

        return ['valid' => true, 'source' => 'local', 'message' => 'PAN format is valid.'];
    }

    public function providerEnabled(): bool
    {
        return ! empty(config('services.kyc.url')) && ! empty(config('services.kyc.key'));
    }

    /**
     * Generic paid-provider call. Expects a JSON response containing a
     * boolean-ish "valid" (or "success") key plus an optional "message".
     * Returns null when the provider errored or is unconfigured.
     */
    private function providerCheck(array $payload): ?array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.kyc.key'),
                    'Accept' => 'application/json',
                ])
                ->post(config('services.kyc.url'), $payload);

            $body = $response->json();
            if ($response->successful() && is_array($body)) {
                $valid = (bool) ($body['valid'] ?? $body['success'] ?? false);
                return [
                    'valid' => $valid,
                    'message' => $body['message'] ?? ($valid ? 'Verification successful.' : 'Verification failed.'),
                ];
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    private function verhoeff(string $number): bool
    {
        $d = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
            [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
            [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
            [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
            [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
            [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
            [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
            [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
            [9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
        ];
        $p = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
            [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
            [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
            [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
            [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
            [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
            [7, 0, 4, 6, 9, 1, 3, 2, 5, 8],
        ];
        $inv = [0, 4, 3, 2, 1, 5, 6, 7, 8, 9];

        $c = 0;
        foreach (array_reverse(array_map('intval', str_split($number))) as $i => $digit) {
            $c = $d[$c][$p[$i % 8][$digit]];
        }

        return $c === 0;
    }
}