<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use App\Traits\UploadFile;

class HomepageController extends Controller
{
    use UploadFile;
    protected $sections = [

        'hero' => 'Hero',

        'why_choose_us' => 'Why Choose Us',

        'featured_categories' => 'Featured Categories',

        'featured_services' => 'Featured Services',

        'featured_sellers' => 'Featured Sellers',

        'latest_projects' => 'Latest Projects',

        'testimonials' => 'Testimonials',

        'faq' => 'FAQ',

        'cta' => 'Call To Action',

        'newsletter' => 'Newsletter',

    ];

    public function index(Request $request)
    {
        $sections = HomepageSection::query()

            ->when($request->search, function ($q) use ($request) {

                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('section_key', 'like', '%' . $request->search . '%');
            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('status', $request->status);
            })

            ->orderBy('sort_order')

            ->paginate(20);

        return view('admin.website.homepage.index', compact('sections'));
    }

    public function create()
    {
        $sections = $this->sections;

        return view('admin.website.homepage.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_key'=>'required|unique:homepage_sections',
            'title' => 'required|max:255',
            'subtitle' => 'nullable|max:255',
            'description' => 'nullable',
            'sort_order' => 'nullable|integer',
            'status' => 'required',
            'button_text' => 'nullable|max:255',
            'button_url' => 'nullable|max:255',
            'icon' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $image = null;
        $backgroundImage = null;

        if ($request->hasFile('image')) {

            $image = $this->uploadFile(
                $request->file('image'),
                'homepage'
            );
        }

        if ($request->hasFile('background_image')) {

            $backgroundImage = $this->uploadFile(
                $request->file('background_image'),
                'homepage'
            );
        }

        HomepageSection::create([
            'section_key' => $request->section_key,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sort_order' => $request->sort_order,
            'status' => $request->status,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'icon' => $request->icon,
            'image' => $image,
            'background_image' => $backgroundImage,
        ]);

        return redirect()

            ->route('admin.website.homepage.index')

            ->with('success', 'Homepage section created successfully.');
    }

    public function show($id)
    {
        $homepage = HomepageSection::findOrFail($id);

        return view('admin.website.homepage.show', compact('homepage'));
    }

    public function edit($id)
    {
        $homepage = HomepageSection::findOrFail($id);

        $sections = $this->sections;

        return view('admin.website.homepage.edit', compact('homepage', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $homepage = HomepageSection::findOrFail($id);

        $request->validate([

            'section_key'=>'required|unique:homepage_sections,section_key,'.$homepage->id,

            'title' => 'required|max:255',

            'subtitle' => 'nullable|max:255',

            'description' => 'nullable',

            'sort_order' => 'nullable|integer',

            'status' => 'required',

            'button_text' => 'nullable|max:255',

            'button_url' => 'nullable|max:255',

            'icon' => 'nullable|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

        ]);

        $data = [

            'section_key' => $request->section_key,

            'title' => $request->title,

            'subtitle' => $request->subtitle,

            'description' => $request->description,

            'sort_order' => $request->sort_order,

            'status' => $request->status,

            'button_text' => $request->button_text,

            'button_url' => $request->button_url,

            'icon' => $request->icon,

        ];

        // Upload New Image
        if ($request->hasFile('image')) {

            $this->deleteFile($homepage->image);

            $data['image'] = $this->uploadFile(
                $request->file('image'),
                'homepage'
            );
        }

        // Upload New Background Image
        if ($request->hasFile('background_image')) {

            $this->deleteFile($homepage->background_image);

            $data['background_image'] = $this->uploadFile(
                $request->file('background_image'),
                'homepage'
            );
        }

        $homepage->update($data);

        return redirect()
            ->route('admin.website.homepage.index')
            ->with('success', 'Homepage section updated successfully.');
    }

    public function destroy($id)
    {
        $homepage = HomepageSection::findOrFail($id);

        // Delete Images
        $this->deleteFile($homepage->image);
        $this->deleteFile($homepage->background_image);

        $homepage->delete();

        return redirect()
            ->route('admin.website.homepage.index')
            ->with('success', 'Homepage section deleted successfully.');
    }
}
