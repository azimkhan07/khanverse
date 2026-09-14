<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\BuyerProfile;
use App\Models\Seller;
use App\Models\SellerProfile;
use App\Models\User;

class ProfileService
{
    public static function isComplete(User $user): bool
    {
        return empty(self::completeness($user)['missing']);
    }

    public static function completenessPercent(User $user): int
    {
        if ($user->role === 'admin') {
            return 100;
        }

        $record = $user->role === 'seller' ? $user->seller : ($user->buyer ?: $user->seller);
        $profile = $record ? $record->profile : null;

        $points = [];
        $points['full_name'] = ! empty(trim((string) ($record->full_name ?? $user->name ?? '')));
        $points['phone'] = ! empty(trim((string) ($user->phone ?: ($profile?->phone ?? ''))));
        $points['avatar'] = ! empty(trim((string) ($record->profile_image ?? '')));
        $points['country'] = ! empty(self::countryName($user, $record, $profile));
        $points['state'] = (bool) ($profile?->state_id ?? null);
        $points['city'] = ! empty(self::cityName($user, $record, $profile));
        $points['address'] = ! empty(trim((string) ($profile?->address ?? '')));
        $points['postal_code'] = ! empty(trim((string) ($profile?->postal_code ?? '')));
        $points['bio'] = ! empty(trim((string) ($profile?->bio ?? '')));

        if ($user->role === 'seller' && $record) {
            $points['service_types'] = $record->serviceTypes()->count() > 0;
            $points['kyc'] = ! empty($record->aadhaar_document) && ! empty($record->pan_document);
            $points['category_details'] = self::categoryDetailsFilledRatio($record);
        }

        if ($user->role === 'buyer' && $record && (bool) $record->is_consultancy) {
            $points['kyc'] = ! empty($record->verification_document);
        }

        $done = array_sum($points);
        $total = count($points);

        return $total ? (int) round($done / $total * 100) : 100;
    }

    public static function completeness(User $user): array
    {
        if ($user->role === 'admin') {
            return ['complete' => true, 'missing' => []];
        }

        if ($user->role === 'seller') {
            $record = $user->seller;
            $profile = $record ? $record->profile : null;
        } else {
            $record = $user->buyer;
            $profile = $record ? $record->profile : null;
        }

        $missing = [];

        if (!$record) {
            $missing[] = 'profile';
        } elseif (empty(trim((string) $record->full_name))) {
            $missing[] = 'full_name';
        }

        if ($user->role === 'seller' && $record && $record->serviceTypes()->count() === 0) {
            $missing[] = 'service_types';
        }

        if (!$profile) {
            foreach (['phone', 'address', 'city', 'country'] as $field) {
                $missing[] = $field;
            }
        } else {
            if (empty(trim((string) ($user->phone ?: $profile->phone)))) {
                $missing[] = 'phone';
            }
            if (empty(trim((string) $profile->address))) {
                $missing[] = 'address';
            }
            if (empty(self::cityName($user, $record, $profile))) {
                $missing[] = 'city';
            }
            if (empty(self::countryName($user, $record, $profile))) {
                $missing[] = 'country';
            }
        }

        // KYC / proof-of-identity requirements.
        if ($user->role === 'seller' && $record) {
            if (empty($record->aadhaar_document) || empty($record->pan_document)) {
                $missing[] = 'kyc';
            }
        }

        // Category-specific (non-technical / field) profile details.
        if ($user->role === 'seller' && $record && self::categoryDetailsFilledRatio($record) < 1) {
            $missing[] = 'category_details';
        }

        if ($user->role === 'buyer' && $record && (bool) $record->is_consultancy) {
            if (empty($record->verification_document)) {
                $missing[] = 'kyc';
            }
        }

        return [
            'complete' => empty($missing),
            'missing' => array_values(array_unique($missing)),
        ];
    }

    public static function data(User $user): array
    {
        $record = $user->role === 'seller' ? $user->seller : ($user->buyer ?: $user->seller);
        $profile = $record ? $record->profile : null;

        $completeness = self::completeness($user);

        $recordData = null;
        if ($record) {
            $recordData = [
                'full_name' => $record->full_name,
                'company_name' => $record->company_name ?? null,
                'country' => self::countryName($user, $record, $profile),
                'city' => self::cityName($user, $record, $profile),
                'profile_image' => $record->profile_image ?? null,
            ];
        }

        $profileData = null;
        if ($profile) {
            $profileData = [
                'phone' => $profile->phone,
                'gender' => $profile->gender,
                'dob' => $profile->dob ? $profile->dob->format('Y-m-d') : null,
                'country_id' => $profile->country_id,
                'state_id' => $profile->state_id,
                'city_id' => $profile->city_id,
                'postal_code' => $profile->postal_code,
                'address' => $profile->address,
                'bio' => $profile->bio,
                'state' => $profile->state_id ? optional($profile->state)->name : null,
                'city' => $profile->city_id ? optional($profile->city)->name : null,
                'country' => $profile->country_id ? optional($profile->country)->name : null,
            ];
        }

        $serviceTypes = null;
        if ($user->role === 'seller' && $record) {
            $serviceTypes = $record->serviceTypes()
                ->get(['service_types.id', 'service_types.name', 'service_types.category_id'])
                ->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'category_id' => $t->category_id,
                ]);
        }

        $categoryDetails = null;
        if ($user->role === 'seller' && $record && $record->categoryDetail) {
            $categoryDetails = [
                'category_id' => $record->categoryDetail->category_id,
                'category_name' => optional($record->categoryDetail->category)->name,
                'data' => $record->categoryDetail->data ?: [],
            ];
        }

        $location = null;
        if ($user->role === 'seller' && $record) {
            $location = [
                'latitude' => $record->latitude !== null ? (float) $record->latitude : null,
                'longitude' => $record->longitude !== null ? (float) $record->longitude : null,
                'location_source' => $record->location_source,
                'location_updated_at' => $record->location_updated_at,
            ];
        }

        $kyc = null;
        if ($user->role === 'seller' && $record) {
            $kyc = [
                'type' => 'seller',
                'aadhaar_uploaded' => (bool) $record->aadhaar_document,
                'pan_uploaded' => (bool) $record->pan_document,
                'status' => $record->kyc_status,
                'rejection_reason' => $record->kyc_rejection_reason,
            ];
        }

        if ($user->role === 'buyer' && $record) {
            $kyc = [
                'type' => 'buyer',
                'is_consultancy' => (bool) $record->is_consultancy,
                'verification_uploaded' => (bool) $record->verification_document,
                'status' => $record->verification_status,
                'rejection_reason' => $record->verification_rejection_reason,
            ];
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?: ($profile ? $profile->phone : null),
            'role' => $user->role,
            'has_buyer' => (bool) $user->buyer,
            'has_seller' => (bool) $user->seller,
            'avatar' => $user->display_image,
            'display_name' => $user->display_name,
            'complete' => $completeness['complete'],
            'missing' => $completeness['missing'],
            'completeness_percent' => self::completenessPercent($user),
            'record' => $recordData,
            'profile' => $profileData,
            'service_types' => $serviceTypes,
            'category_details' => $categoryDetails,
            'location' => $location,
            'kyc' => $kyc,
        ];
    }

    protected static function cityName(User $user, $record, $profile): ?string
    {
        if ($profile && $profile->city_id) {
            return optional($profile->city)->name;
        }
        return $record ? $record->city : null;
    }

    protected static function countryName(User $user, $record, $profile): ?string
    {
        if ($profile && $profile->country_id) {
            return optional($profile->country)->name;
        }
        return $record ? $record->country : null;
    }

    /**
     * Field-type (non-technical) categories tied to a seller, either through their
     * selected category details or the categories of their service types.
     */
    protected static function fieldCategories($record): array
    {
        if (! $record) {
            return [];
        }

        $cats = [];
        if ($record->categoryDetail && $record->categoryDetail->category_id) {
            $cats[$record->categoryDetail->category_id] = $record->categoryDetail->category;
        }
        foreach ($record->serviceTypes()->with('category')->get() as $type) {
            if ($type->category && $type->category->category_type === 'field') {
                $cats[$type->category->id] = $type->category;
            }
        }
        return array_values($cats);
    }

    /**
     * Fraction (0..1) of required, non-checkbox category fields completed by the seller.
     */
    protected static function categoryDetailsFilledRatio($record): float
    {
        $categories = self::fieldCategories($record);
        if (empty($categories)) {
            return 1.0;
        }

        $requiredKeys = [];
        foreach ($categories as $cat) {
            foreach ((array) ($cat->form_fields ?? []) as $field) {
                if (! empty($field['required']) && ($field['type'] ?? '') !== 'checkbox') {
                    $requiredKeys[] = $field['key'];
                }
            }
        }
        $requiredKeys = array_values(array_unique($requiredKeys));
        if (empty($requiredKeys)) {
            return 1.0;
        }

        $data = $record->categoryDetail?->data ?: [];
        $filled = 0;
        foreach ($requiredKeys as $key) {
            $value = $data[$key] ?? null;
            if (is_array($value)) {
                $nonEmpty = array_filter($value, fn($v) => $v !== '' && $v !== null);
                if (count($nonEmpty) > 0) {
                    $filled++;
                }
            } elseif (is_numeric($value) || (is_string($value) && trim($value) !== '') || is_bool($value)) {
                $filled++;
            }
        }

        return $filled / count($requiredKeys);
    }
}