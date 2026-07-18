<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::query()

            ->when($request->search, function ($q) use ($request) {

                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%');
            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('status', $request->status);
            })

            ->latest()

            ->paginate(20);

        return view('admin.website.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.website.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'title' => 'required|max:255',

            'description' => 'nullable',

            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'meta_title' => 'nullable|max:255',

            'meta_keywords' => 'nullable',

            'meta_description' => 'nullable',

            'status' => 'required',

        ]);

        $bannerImage = null;

        if ($request->hasFile('banner_image')) {

            $bannerImage = $this->uploadFile(
                $request->file('banner_image'),
                'pages'
            );
        }

        Page::create([

            'title' => $request->title,

            'slug' => Str::slug($request->title),

            'description' => $request->description,

            'banner_image' => $bannerImage,

            'meta_title' => $request->meta_title,

            'meta_keywords' => $request->meta_keywords,

            'meta_description' => $request->meta_description,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function show($id)
    {
        $page = Page::findOrFail($id);

        return view('admin.website.pages.show', compact('page'));
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);

        return view('admin.website.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([

            'title' => 'required|max:255',

            'description' => 'nullable',

            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'meta_title' => 'nullable|max:255',

            'meta_keywords' => 'nullable',

            'meta_description' => 'nullable',

            'status' => 'required',

        ]);

        $data = [

            'title' => $request->title,

            'slug' => Str::slug($request->title),

            'description' => $request->description,

            'meta_title' => $request->meta_title,

            'meta_keywords' => $request->meta_keywords,

            'meta_description' => $request->meta_description,

            'status' => $request->status,

        ];

        if ($request->hasFile('banner_image')) {

            $this->deleteFile($page->banner_image);

            $data['banner_image'] = $this->uploadFile(
                $request->file('banner_image'),
                'pages'
            );
        }

        $page->update($data);

        return redirect()
            ->route('admin.website.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        $this->deleteFile($page->banner_image);

        $page->delete();

        return redirect()
            ->route('admin.website.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
