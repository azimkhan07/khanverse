<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $testimonials = Testimonial::query()

            ->when($request->search, function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('designation', 'like', '%' . $request->search . '%')
                    ->orWhere('company', 'like', '%' . $request->search . '%');
            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('status', $request->status);
            })

            ->orderBy('sort_order')

            ->paginate(20);

        return view('admin.website.testimonial.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.website.testimonial.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required|max:255',

            'designation' => 'nullable|max:255',

            'company' => 'nullable|max:255',

            'review' => 'nullable',

            'rating' => 'required|integer|min:1|max:5',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        $image = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image')->store('testimonials', 'public');
        }

        Testimonial::create([

            'name' => $request->name,

            'designation' => $request->designation,

            'company' => $request->company,

            'review' => $request->review,

            'rating' => $request->rating,

            'sort_order' => $request->sort_order ?? 0,

            'status' => $request->status,

            'image' => $image,

        ]);

        return redirect()
            ->route('admin.website.testimonial.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function show(Testimonial $testimonial)
    {
        return view('admin.website.testimonial.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.website.testimonial.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([

            'name' => 'required|max:255',

            'designation' => 'nullable|max:255',

            'company' => 'nullable|max:255',

            'review' => 'nullable',

            'rating' => 'required|integer|min:1|max:5',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        $image = $testimonial->image;

        if ($request->hasFile('image')) {

            if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {

                Storage::disk('public')->delete($testimonial->image);
            }

            $image = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update([

            'name' => $request->name,

            'designation' => $request->designation,

            'company' => $request->company,

            'review' => $request->review,

            'rating' => $request->rating,

            'sort_order' => $request->sort_order ?? 0,

            'status' => $request->status,

            'image' => $image,

        ]);

        return redirect()
            ->route('admin.website.testimonial.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {

            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        return redirect()
            ->route('admin.website.testimonial.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
}
