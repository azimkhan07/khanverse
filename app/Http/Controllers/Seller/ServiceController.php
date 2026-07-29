<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $sellerId = Auth::user()->seller->id;
        $services = Service::with('category')->where('seller_id', $sellerId)->latest()->paginate(20);

        return view('seller.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();

        return view('seller.services.create', compact('categories'));
    }

    public function show(Service $service)
    {
        abort_if($service->seller_id != Auth::user()->seller->id, 403);

        $service->load(['category', 'images']);

        return view('seller.services.show', compact('service'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required',
            'slug'           => 'required|unique:services,slug',
            'description'    => 'required',
            'price'          => 'required|numeric',
            'delivery_days'  => 'required|integer',
            'revisions'      => 'nullable|integer',
            'category_id'    => 'required|exists:categories,id',
            'status'         => 'required',
            'thumbnail'      => 'nullable|image',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }

        $data['seller_id'] = Auth::user()->seller->id;

        Service::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Service Created Successfully',
            'reload' => true,
        ]);
    }

    public function edit(Service $service)
    {
        abort_if($service->seller_id != Auth::user()->seller->id, 403);
        $categories = Category::where('status', 1)->get();

        return view('seller.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        abort_if($service->seller_id != Auth::user()->seller->id, 403);

        $data = $request->validate([
            'title'         => 'required',
            'slug'          => 'required|unique:services,slug,' . $service->id,
            'description'   => 'required',
            'price'         => 'required|numeric',
            'delivery_days' => 'required|integer',
            'revisions'     => 'nullable|integer',
            'category_id'   => 'required|exists:categories,id',
            'status'        => 'required',
            'thumbnail'     => 'nullable|image',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($service->thumbnail) {
                Storage::disk('public')->delete($service->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }

        $service->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Service Updated Successfully',
            'reload' => true,
        ]);
    }

    public function destroy(Service $service)
    {
        abort_if($service->seller_id != Auth::user()->seller->id,403);

        foreach ($service->images as $image) {
            if ($image->image) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
        }

        if ($service->thumbnail) {
            Storage::disk('public')->delete($service->thumbnail);
        }

        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service Deleted Successfully',
        ]);
    }

    public function gallery(Service $service)
    {
        abort_if($service->seller_id != Auth::user()->seller->id, 403);

        $images = ServiceImage::where('service_id', $service->id)->latest()->get();

        return view('seller.services.gallery', compact('service', 'images'));
    }

    public function galleryStore(Request $request, Service $service)
    {
        abort_if($service->seller_id != Auth::user()->seller->id, 403);

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store('services/gallery', 'public');
            ServiceImage::create([
                'service_id' => $service->id,
                'image' => $path,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Gallery Images Uploaded Successfully',
            'reload' => true,
        ]);
    }

    public function galleryDelete(ServiceImage $image)
    {
        abort_if($image->service->seller_id != Auth::user()->seller->id, 403);

        if ($image->image) {
            Storage::disk('public')->delete(
                $image->image
            );
        }

        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image Deleted Successfully',
            'reload' => true,
        ]);
    }
}
