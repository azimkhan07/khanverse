<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Auth::user()->seller;

        $query = Service::with(['category', 'images'])
            ->where('seller_id', $seller->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $services = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($services);
    }

    public function show($id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $service = Service::with(['category', 'images'])
            ->where('seller_id', $seller->id)
            ->findOrFail($id);

        return response()->json($service);
    }

    public function store(Request $request): JsonResponse
    {
        $seller = Auth::user()->seller;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'delivery_days' => 'required|integer|min:1',
            'revisions' => 'required|integer|min:0',
            'delivery_method' => 'sometimes|in:digital,hosting',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }

        $validated['seller_id'] = $seller->id;

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service created successfully.',
            'service' => $service,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $service = Service::where('seller_id', $seller->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:services,slug,' . $id,
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'delivery_days' => 'sometimes|required|integer|min:1',
            'revisions' => 'sometimes|required|integer|min:0',
            'delivery_method' => 'sometimes|in:digital,hosting',
            'category_id' => 'sometimes|required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($service->thumbnail) {
                Storage::disk('public')->delete($service->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }

        $service->update($validated);

        return response()->json([
            'message' => 'Service updated successfully.',
            'service' => $service,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $service = Service::where('seller_id', $seller->id)->findOrFail($id);

        if ($service->thumbnail) {
            Storage::disk('public')->delete($service->thumbnail);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully.',
        ]);
    }

    public function gallery($id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $service = Service::with('images')->where('seller_id', $seller->id)->findOrFail($id);

        return response()->json([
            'service' => $service,
            'images' => $service->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'image' => asset('storage/' . $img->image),
                    'path' => $img->image,
                ];
            }),
        ]);
    }

    public function uploadGallery(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $service = Service::where('seller_id', $seller->id)->findOrFail($id);

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:4096',
        ]);

        $uploaded = [];
        foreach ($request->file('images') as $file) {
            $path = $file->store('services/' . $service->id . '/gallery', 'public');
            $img = ServiceImage::create([
                'service_id' => $service->id,
                'image' => $path,
            ]);
            $uploaded[] = [
                'id' => $img->id,
                'image' => asset('storage/' . $path),
                'path' => $path,
            ];
        }

        return response()->json([
            'message' => 'Image(s) uploaded successfully.',
            'images' => $uploaded,
        ], 201);
    }

    public function deleteGalleryImage($serviceId, $imageId): JsonResponse
    {
        $seller = Auth::user()->seller;

        $service = Service::where('seller_id', $seller->id)->findOrFail($serviceId);

        $image = ServiceImage::where('service_id', $service->id)->findOrFail($imageId);

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully.',
        ]);
    }
}
