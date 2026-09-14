<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ServiceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Auth::user()->seller;

        $query = Service::with(['category', 'images', 'serviceTypes'])
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

        $service = Service::with(['category', 'images', 'serviceTypes'])
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
            'service_type_ids' => 'nullable|array',
            'service_type_ids.*' => 'integer|exists:service_types,id',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'boolean',
            'visit_start_time' => 'nullable|date_format:H:i',
            'visit_end_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'working_days.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $this->requireTimingForFieldCategory($validated, $validated['category_id']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }

        $validated['seller_id'] = $seller->id;

        $service = Service::create($validated);

        if (!empty($validated['service_type_ids'])) {
            $service->serviceTypes()->sync($validated['service_type_ids']);
        }

        return response()->json([
            'message' => 'Service created successfully.',
            'service' => $service->fresh(['category', 'serviceTypes']),
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
            'service_type_ids' => 'nullable|array',
            'service_type_ids.*' => 'integer|exists:service_types,id',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'boolean',
            'visit_start_time' => 'nullable|date_format:H:i',
            'visit_end_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'working_days.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $this->requireTimingForFieldCategory($validated, $validated['category_id'] ?? $service->category_id);

        if ($request->hasFile('thumbnail')) {
            if ($service->thumbnail) {
                Storage::disk('public')->delete($service->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }

        $service->update($validated);

        if (array_key_exists('service_type_ids', $validated)) {
            $service->serviceTypes()->sync($validated['service_type_ids'] ?? []);
        }

        return response()->json([
            'message' => 'Service updated successfully.',
            'service' => $service->fresh(['category', 'serviceTypes']),
        ]);
    }

    private function requireTimingForFieldCategory(array $validated, $categoryId): void
    {
        $category = Category::find($categoryId);
        if (! $category || $category->category_type !== 'field') {
            return;
        }

        $errors = [];
        if (empty($validated['visit_start_time'])) {
            $errors['visit_start_time'] = ['Working hours start time is required for this service category.'];
        }
        if (empty($validated['visit_end_time'])) {
            $errors['visit_end_time'] = ['Working hours end time is required for this service category.'];
        }
        if (empty($validated['working_days']) || count($validated['working_days']) === 0) {
            $errors['working_days'] = ['Select at least one working day for this service category.'];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
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
