<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')
            ->latest()
            ->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();

        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:services,slug',
            'description' => 'required',
            'price' => 'required|numeric',
            'delivery_days' => 'required|integer',
            'revisions' => 'nullable|integer',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required',
            'thumbnail' => 'nullable|image'
        ]);

        if ($request->hasFile('thumbnail')) {

            $data['thumbnail'] = $request
                ->file('thumbnail')
                ->store('services', 'public');
        }

        Service::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Service Created Successfully',
            'reload' => true
        ]);
    }

    public function edit(Service $service)
    {

        $categories = Category::where('status', 1)->get();

        return view(
            'admin.services.edit',
            compact('service', 'categories')
        );
    }

    public function update(
        Request $request,
        Service $service
    ) {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:services,slug,' . $service->id,
            'description' => 'required',
            'price' => 'required|numeric',
            'delivery_days' => 'required|integer',
            'revisions' => 'nullable|integer',
            'category_id' => 'required',
            'status' => 'required',
            'thumbnail' => 'nullable|image'
        ]);

        if ($request->hasFile('thumbnail')) {

            $data['thumbnail'] = $request
                ->file('thumbnail')
                ->store('services', 'public');
        }

        $service->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Service Updated Successfully',
            'reload' => true
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service Deleted Successfully'
        ]);
    }

    // service image 

    public function gallery(Service $service)
    {
        $images = ServiceImage::where(
            'service_id',
            $service->id
        )->get();

        return view(
            'admin.services.gallery',
            compact('service', 'images')
        );
    }

    public function galleryStore(Request $request, Service $service)
    {
        $request->validate([
            'images'   => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        foreach ($request->file('images') as $image) {

            $path = $image->store('services/gallery', 'public');

            ServiceImage::create([
                'service_id' => $service->id,
                'image'      => $path,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Gallery Images Uploaded Successfully',
            'reload'  => true,
        ]);
    }

    public function galleryDelete(ServiceImage $image)
    {
        if ($image->image) {

            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Image Deleted Successfully',
            'reload'  => true,
        ]);
    }
}
