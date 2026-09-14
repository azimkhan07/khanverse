<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\BuyerProfile;
use App\Models\Seller;
use App\Models\SellerCategoryDetail;
use App\Models\SellerProfile;
use App\Services\KycVerificationService;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicProfileController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'profile' => ProfileService::data(Auth::user()),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'company_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'intent' => ['nullable', 'in:buyer,seller,user'],
            'service_type_ids' => ['nullable', 'array'],
            'service_type_ids.*' => ['integer', 'exists:service_types,id'],
            'aadhaar_number' => ['nullable', 'string', 'max:20', $this->kycAadhaarRule()],
            'pan_number' => ['nullable', 'string', 'max:20', $this->kycPanRule()],
            'aadhaar_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'pan_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'is_consultancy' => ['nullable', 'boolean'],
            'verification_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'category_id' => ['nullable', 'integer'],
            'category_details' => ['nullable', 'array'],
        ]);

        $fullName = $validated['full_name'] ?? $validated['name'] ?? $user->name;

        // A plain "user" completing their profile — turn them into the requested role.
        if ($user->role === 'user' && in_array($validated['intent'] ?? null, ['buyer', 'seller'], true)) {
            $targetRole = $validated['intent'];
            $user->update(['role' => $targetRole]);
            $user = $user->fresh();
        }

        $isSeller = $user->role === 'seller';
        $currentRecord = $isSeller ? $user->seller : $user->buyer;

        $aadhaarDoc = $request->hasFile('aadhaar_document') ? $request->file('aadhaar_document')->store('seller-kyc', 'public') : null;
        $panDoc = $request->hasFile('pan_document') ? $request->file('pan_document')->store('seller-kyc', 'public') : null;
        $verificationDoc = $request->hasFile('verification_document') ? $request->file('verification_document')->store('buyer-verification', 'public') : null;

        if ($isSeller) {
            $record = Seller::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'full_name' => $fullName,
                    'country' => $validated['country'] ?? $currentRecord?->country ?? null,
                    'city' => $validated['city'] ?? $currentRecord?->city ?? null,
                    'aadhaar_number' => $validated['aadhaar_number'] ?? $currentRecord?->aadhaar_number ?? null,
                    'pan_number' => $validated['pan_number'] ?? $currentRecord?->pan_number ?? null,
                    'aadhaar_document' => $aadhaarDoc ?? $currentRecord?->aadhaar_document ?? null,
                    'pan_document' => $panDoc ?? $currentRecord?->pan_document ?? null,
                    'kyc_status' => ($aadhaarDoc || $panDoc) ? 'submitted' : ($currentRecord?->kyc_status ?? 'pending'),
                ]
            );

            if (array_key_exists('service_type_ids', $validated)) {
                $record->serviceTypes()->sync($validated['service_type_ids'] ?? []);
            }

            if (array_key_exists('latitude', $validated) && is_numeric($validated['latitude'])
                && array_key_exists('longitude', $validated) && is_numeric($validated['longitude'])) {
                $record->update([
                    'latitude' => (float)$validated['latitude'],
                    'longitude' => (float)$validated['longitude'],
                    'location_source' => 'profile',
                    'location_updated_at' => now(),
                ]);
            }

            if (array_key_exists('category_details', $validated) || array_key_exists('category_id', $validated)) {
                SellerCategoryDetail::updateOrCreate(
                    ['seller_id' => $record->id],
                    [
                        'seller_id' => $record->id,
                        'category_id' => $validated['category_id'] ?? $record->categoryDetail?->category_id ?? null,
                        'data' => $validated['category_details'] ?? $record->categoryDetail?->data ?? null,
                    ]
                );
            }

            SellerProfile::updateOrCreate(
                ['seller_id' => $record->id],
                [
                    'seller_id' => $record->id,
                    'phone' => $validated['phone'] ?? Auth::user()->phone ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'dob' => $validated['dob'] ?? null,
                    'country_id' => $validated['country_id'] ?? $currentRecord?->profile?->country_id ?? null,
                    'state_id' => $validated['state_id'] ?? $currentRecord?->profile?->state_id ?? null,
                    'city_id' => $validated['city_id'] ?? $currentRecord?->profile?->city_id ?? null,
                    'postal_code' => $validated['postal_code'] ?? $currentRecord?->profile?->postal_code ?? null,
                    'address' => $validated['address'] ?? $currentRecord?->profile?->address ?? null,
                    'bio' => $validated['bio'] ?? $currentRecord?->profile?->bio ?? null,
                ]
            );
        } else {
            $record = Buyer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'full_name' => $fullName,
                    'company_name' => $validated['company_name'] ?? $currentRecord?->company_name ?? null,
                    'country' => $validated['country'] ?? $currentRecord?->country ?? null,
                    'city' => $validated['city'] ?? $currentRecord?->city ?? null,
                    'is_consultancy' => $request->has('is_consultancy')
                        ? $request->boolean('is_consultancy')
                        : (bool) ($currentRecord?->is_consultancy ?? false),
                    'verification_document' => $verificationDoc ?? $currentRecord?->verification_document ?? null,
                    'verification_status' => $verificationDoc ? 'submitted' : ($currentRecord?->verification_status ?? 'pending'),
                ]
            );

            BuyerProfile::updateOrCreate(
                ['buyer_id' => $record->id],
                [
                    'buyer_id' => $record->id,
                    'phone' => $validated['phone'] ?? Auth::user()->phone ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'dob' => $validated['dob'] ?? null,
                    'country_id' => $validated['country_id'] ?? $currentRecord?->profile?->country_id ?? null,
                    'state_id' => $validated['state_id'] ?? $currentRecord?->profile?->state_id ?? null,
                    'city_id' => $validated['city_id'] ?? $currentRecord?->profile?->city_id ?? null,
                    'postal_code' => $validated['postal_code'] ?? $currentRecord?->profile?->postal_code ?? null,
                    'address' => $validated['address'] ?? $currentRecord?->profile?->address ?? null,
                    'bio' => $validated['bio'] ?? $currentRecord?->profile?->bio ?? null,
                ]
            );
        }

        if ($request->hasFile('avatar')) {
            if ($record->profile_image) {
                Storage::disk('public')->delete($record->profile_image);
            }
            $record->update([
                'profile_image' => $request->file('avatar')->store($isSeller ? 'seller-avatars' : 'buyer-avatars', 'public'),
            ]);
        }

        $user->update([
            'name' => $fullName,
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => ProfileService::data($user->fresh()),
        ]);
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
}