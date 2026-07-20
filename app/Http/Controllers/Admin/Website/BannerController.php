<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::query()

            ->when($request->search, function ($q) use ($request) {

                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%');

            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('status', $request->status);

            })

            ->orderBy('id', 'desc')

            ->paginate(20);

        return view('admin.website.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.website.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'title' => 'required|max:255',

            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

            'link' => 'nullable|url',

            'position' => 'required',

            'status' => 'required|boolean',

        ]);

        $image = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image')->store('banners', 'public');

        }

        Banner::create([

            'title' => $request->title,

            'image' => $image,

            'link' => $request->link,

            'position' => $request->position,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    public function show(Banner $banner)
    {
        return view('admin.website.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('admin.website.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([

            'title' => 'required|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'link' => 'nullable|url',

            'position' => 'required',

            'status' => 'required|boolean',

        ]);

        $image = $banner->image;

        if ($request->hasFile('image')) {

            if ($banner->image && Storage::disk('public')->exists($banner->image)) {

                Storage::disk('public')->delete($banner->image);

            }

            $image = $request->file('image')->store('banners', 'public');

        }

        $banner->update([

            'title' => $request->title,

            'image' => $image,

            'link' => $request->link,

            'position' => $request->position,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {

            Storage::disk('public')->delete($banner->image);

        }

        $banner->delete();

        return redirect()
            ->route('admin.website.banners.index')
            ->with('success', 'Banner deleted successfully.');
    }
}
