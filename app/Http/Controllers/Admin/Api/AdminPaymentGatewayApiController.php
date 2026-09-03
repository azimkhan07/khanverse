<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPaymentGatewayApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $gateways = PaymentGateway::latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json($gateways);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'gateway' => PaymentGateway::findOrFail($id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9_]+$/',
            'merchant_id' => 'nullable|string|max:255',
            'access_code' => 'required|string|max:255',
            'working_key' => 'required|string|max:255',
            'endpoint' => 'required|string|max:500',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['name'], '_');

        if (PaymentGateway::where('slug', $slug)->exists()) {
            return response()->json([
                'message' => 'A gateway with this slug already exists. Pick a unique slug.',
            ], 422);
        }

        $isActive = $request->boolean('is_active', true);
        $isDefault = $request->boolean('is_default', false);

        if ($isDefault) {
            PaymentGateway::where('is_default', true)->update(['is_default' => false]);
        }

        if ($isActive) {
            $this->deactivateOthers();
        }

        $gateway = PaymentGateway::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'merchant_id' => $validated['merchant_id'] ?? null,
            'access_code' => $validated['access_code'],
            'working_key' => $validated['working_key'],
            'endpoint' => $validated['endpoint'],
            'is_default' => $isDefault,
            'is_active' => $isActive,
        ]);

        return response()->json([
            'message' => 'Payment gateway added. Only one gateway stays active at a time.',
            'gateway' => $gateway,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $gateway = PaymentGateway::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9_]+$/',
            'merchant_id' => 'nullable|string|max:255',
            'access_code' => 'sometimes|required|string|max:255',
            'working_key' => 'sometimes|required|string|max:255',
            'endpoint' => 'sometimes|required|string|max:500',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = isset($validated['slug']) && $validated['slug']
            ? $validated['slug']
            : Str::slug($validated['name'] ?? $gateway->name, '_');

        if (PaymentGateway::where('slug', $slug)->where('id', '!=', $gateway->id)->exists()) {
            return response()->json([
                'message' => 'A gateway with this slug already exists. Pick a unique slug.',
            ], 422);
        }

        $isActive = $request->boolean('is_active', $gateway->is_active);
        $isDefault = $request->boolean('is_default', $gateway->is_default);

        if ($isDefault) {
            PaymentGateway::where('is_default', true)
                ->where('id', '!=', $gateway->id)
                ->update(['is_default' => false]);
        }

        if ($isActive) {
            $this->deactivateOthers($gateway->id);
        }

        $gateway->update([
            'name' => $validated['name'] ?? $gateway->name,
            'slug' => $slug,
            'merchant_id' => array_key_exists('merchant_id', $validated) ? $validated['merchant_id'] : $gateway->merchant_id,
            'access_code' => $validated['access_code'] ?? $gateway->access_code,
            'working_key' => $validated['working_key'] ?? $gateway->working_key,
            'endpoint' => $validated['endpoint'] ?? $gateway->endpoint,
            'is_default' => $isDefault,
            'is_active' => $isActive,
        ]);

        return response()->json([
            'message' => 'Payment gateway updated.',
            'gateway' => $gateway->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->delete();

        return response()->json([
            'message' => 'Payment gateway deleted.',
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $gateway = PaymentGateway::findOrFail($id);
        $nowActive = ! $gateway->is_active;

        if ($nowActive) {
            $this->deactivateOthers($gateway->id);
        }

        $gateway->update(['is_active' => $nowActive]);

        return response()->json([
            'message' => $nowActive
                ? 'Gateway activated. Other gateways were deactivated automatically.'
                : 'Gateway deactivated.',
            'gateway' => $gateway->fresh(),
        ]);
    }

    public function setDefault($id): JsonResponse
    {
        $gateway = PaymentGateway::findOrFail($id);

        PaymentGateway::where('is_default', true)
            ->where('id', '!=', $gateway->id)
            ->update(['is_default' => false]);

        $this->deactivateOthers($gateway->id);

        $gateway->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => "{$gateway->name} is now the default gateway. All other gateways were deactivated.",
            'gateway' => $gateway->fresh(),
        ]);
    }

    /**
     * Only one gateway can be active at a time - switch all others off.
     */
    private function deactivateOthers(?int $keepAlive = null): void
    {
        PaymentGateway::query()
            ->where('is_active', true)
            ->when($keepAlive, fn ($q) => $q->where('id', '!=', $keepAlive))
            ->update(['is_active' => false]);
    }
}