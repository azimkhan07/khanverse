<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoController extends Controller
{
    public function index(Request $request)
    {
        $seoSettings = SeoSetting::query()

            ->when($request->search, function ($q) use ($request) {

                $q->where('page_key', 'like', '%' . $request->search . '%')
                    ->orWhere('meta_title', 'like', '%' . $request->search . '%');
            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('status', $request->status);
            })

            ->orderBy('page_key')

            ->paginate(20);

        return view('admin.website.seo.index', compact('seoSettings'));
    }

    public function create()
    {
        return view('admin.website.seo.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'page_key' => 'required|unique:seo_settings,page_key',

            'meta_title' => 'nullable|max:255',

            'meta_description' => 'nullable',

            'meta_keywords' => 'nullable',

            'canonical_url' => 'nullable',

            'robots' => 'nullable',

            'og_title' => 'nullable|max:255',

            'og_description' => 'nullable',

            'twitter_title' => 'nullable|max:255',

            'twitter_description' => 'nullable',

            'status' => 'required|boolean',

            'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'twitter_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        $ogImage = null;
        $twitterImage = null;

        if ($request->hasFile('og_image')) {

            $ogImage = $request->file('og_image')->store('seo', 'public');
        }

        if ($request->hasFile('twitter_image')) {

            $twitterImage = $request->file('twitter_image')->store('seo', 'public');
        }

        SeoSetting::create([

            'page_key' => $request->page_key,

            'meta_title' => $request->meta_title,

            'meta_description' => $request->meta_description,

            'meta_keywords' => $request->meta_keywords,

            'canonical_url' => $request->canonical_url,

            'robots' => $request->robots,

            'og_title' => $request->og_title,

            'og_description' => $request->og_description,

            'og_image' => $ogImage,

            'twitter_title' => $request->twitter_title,

            'twitter_description' => $request->twitter_description,

            'twitter_image' => $twitterImage,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.seo.index')
            ->with('success', 'SEO Setting created successfully.');
    }

    public function show(SeoSetting $seo)
    {
        return view('admin.website.seo.show', compact('seo'));
    }

    public function edit(SeoSetting $seo)
    {
        return view('admin.website.seo.edit', compact('seo'));
    }

    public function update(Request $request, SeoSetting $seo)
    {
        $request->validate([

            'page_key' => 'required|unique:seo_settings,page_key,' . $seo->id,

            'meta_title' => 'nullable|max:255',

            'meta_description' => 'nullable',

            'meta_keywords' => 'nullable',

            'canonical_url' => 'nullable',

            'robots' => 'nullable',

            'og_title' => 'nullable|max:255',

            'og_description' => 'nullable',

            'twitter_title' => 'nullable|max:255',

            'twitter_description' => 'nullable',

            'status' => 'required|boolean',

            'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'twitter_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        $ogImage = $seo->og_image;
        $twitterImage = $seo->twitter_image;

        if ($request->hasFile('og_image')) {

            if ($seo->og_image && Storage::disk('public')->exists($seo->og_image)) {

                Storage::disk('public')->delete($seo->og_image);
            }

            $ogImage = $request->file('og_image')->store('seo', 'public');
        }

        if ($request->hasFile('twitter_image')) {

            if ($seo->twitter_image && Storage::disk('public')->exists($seo->twitter_image)) {

                Storage::disk('public')->delete($seo->twitter_image);
            }

            $twitterImage = $request->file('twitter_image')->store('seo', 'public');
        }

        $seo->update([

            'page_key' => $request->page_key,

            'meta_title' => $request->meta_title,

            'meta_description' => $request->meta_description,

            'meta_keywords' => $request->meta_keywords,

            'canonical_url' => $request->canonical_url,

            'robots' => $request->robots,

            'og_title' => $request->og_title,

            'og_description' => $request->og_description,

            'og_image' => $ogImage,

            'twitter_title' => $request->twitter_title,

            'twitter_description' => $request->twitter_description,

            'twitter_image' => $twitterImage,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.seo.index')
            ->with('success', 'SEO Setting updated successfully.');
    }

    public function destroy(SeoSetting $seo)
    {
        if ($seo->og_image && Storage::disk('public')->exists($seo->og_image)) {

            Storage::disk('public')->delete($seo->og_image);
        }

        if ($seo->twitter_image && Storage::disk('public')->exists($seo->twitter_image)) {

            Storage::disk('public')->delete($seo->twitter_image);
        }

        $seo->delete();

        return redirect()
            ->route('admin.website.seo.index')
            ->with('success', 'SEO Setting deleted successfully.');
    }
}
