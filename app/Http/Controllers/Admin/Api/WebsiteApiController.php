<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\MaintenanceSetting;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Models\Testimonial;
use App\Traits\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteApiController extends Controller
{
    use UploadFile;

    private function storageUrl($path)
    {
        return $path ? url('storage/' . $path) : null;
    }

    /* ------------------------------------------------------------------ */
    /* Banners                                                             */
    /* ------------------------------------------------------------------ */
    public function banners(Request $request)
    {
        $banners = Banner::query()
            ->when($request->search, fn($q) => $q->where('title', 'like', "%$request->search%")->orWhere('position', 'like', "%$request->search%"))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10);

        $banners->getCollection()->transform(fn($b) => $b->setAttribute('image_url', $this->storageUrl($b->image)));
        return response()->json($banners);
    }

    public function bannerShow(Banner $banner)
    {
        $banner->setAttribute('image_url', $this->storageUrl($banner->image));
        return response()->json(['status' => true, 'banner' => $banner]);
    }

    public function bannersStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link' => 'nullable|url',
            'position' => 'required',
            'status' => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        if ($request->hasFile('image')) $data['image'] = $this->uploadFile($request->file('image'), 'banners');
        $banner = Banner::create($data);
        $banner->setAttribute('image_url', $this->storageUrl($banner->image));
        return response()->json(['status' => true, 'message' => 'Banner created successfully.', 'banner' => $banner], 201);
    }

    public function bannersUpdate(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link' => 'nullable|url',
            'position' => 'sometimes|required',
            'status' => 'boolean',
        ]);
        if ($request->hasFile('image')) {
            $this->deleteFile($banner->image);
            $data['image'] = $this->uploadFile($request->file('image'), 'banners');
        }
        $data['status'] = $request->has('status') ? $request->boolean('status') : $banner->status;
        $banner->update($data);
        $banner->setAttribute('image_url', $this->storageUrl($banner->image));
        return response()->json(['status' => true, 'message' => 'Banner updated successfully.', 'banner' => $banner]);
    }

    public function bannersDestroy(Banner $banner)
    {
        $this->deleteFile($banner->image);
        $banner->delete();
        return response()->json(['status' => true, 'message' => 'Banner deleted successfully.']);
    }

    public function bannersToggle(Banner $banner)
    {
        $banner->update(['status' => !$banner->status]);
        return response()->json(['status' => true, 'message' => 'Banner status updated.', 'banner' => $banner]);
    }

    /* ------------------------------------------------------------------ */
    /* FAQs                                                                */
    /* ------------------------------------------------------------------ */
    public function faqs(Request $request)
    {
        $faqs = Faq::query()
            ->when($request->search, fn($q) => $q->where('question', 'like', "%$request->search%"))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->orderBy('sort_order')
            ->paginate($request->per_page ?? 10);
        return response()->json($faqs);
    }

    public function faqShow(Faq $faq)
    {
        return response()->json(['status' => true, 'faq' => $faq]);
    }

    public function faqsStore(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|max:255',
            'answer' => 'nullable',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        $data['sort_order'] = $request->sort_order ?? 0;
        $faq = Faq::create($data);
        return response()->json(['status' => true, 'message' => 'FAQ created successfully.', 'faq' => $faq], 201);
    }

    public function faqsUpdate(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => 'sometimes|required|max:255',
            'answer' => 'nullable',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $faq->update($data);
        return response()->json(['status' => true, 'message' => 'FAQ updated successfully.', 'faq' => $faq]);
    }

    public function faqsDestroy(Faq $faq)
    {
        $faq->delete();
        return response()->json(['status' => true, 'message' => 'FAQ deleted successfully.']);
    }

    public function faqsToggle(Faq $faq)
    {
        $faq->update(['status' => !$faq->status]);
        return response()->json(['status' => true, 'message' => 'FAQ status updated.', 'faq' => $faq]);
    }

    /* ------------------------------------------------------------------ */
    /* Testimonials                                                        */
    /* ------------------------------------------------------------------ */
    public function testimonials(Request $request)
    {
        $items = Testimonial::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%$request->search%")->orWhere('company', 'like', "%$request->search%"))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->orderBy('sort_order')
            ->paginate($request->per_page ?? 10);
        $items->getCollection()->transform(fn($t) => $t->setAttribute('image_url', $this->storageUrl($t->image)));
        return response()->json($items);
    }

    public function testimonialShow(Testimonial $testimonial)
    {
        $testimonial->setAttribute('image_url', $this->storageUrl($testimonial->image));
        return response()->json(['status' => true, 'testimonial' => $testimonial]);
    }

    public function testimonialsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'designation' => 'nullable|max:255',
            'company' => 'nullable|max:255',
            'review' => 'nullable',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
        $data['status'] = $request->boolean('status', true);
        $data['sort_order'] = $request->sort_order ?? 0;
        if ($request->hasFile('image')) $data['image'] = $this->uploadFile($request->file('image'), 'testimonials');
        $item = Testimonial::create($data);
        $item->setAttribute('image_url', $this->storageUrl($item->image));
        return response()->json(['status' => true, 'message' => 'Testimonial created successfully.', 'testimonial' => $item], 201);
    }

    public function testimonialsUpdate(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|max:255',
            'designation' => 'nullable|max:255',
            'company' => 'nullable|max:255',
            'review' => 'nullable',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
        if ($request->hasFile('image')) {
            $this->deleteFile($testimonial->image);
            $data['image'] = $this->uploadFile($request->file('image'), 'testimonials');
        }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $testimonial->update($data);
        $testimonial->setAttribute('image_url', $this->storageUrl($testimonial->image));
        return response()->json(['status' => true, 'message' => 'Testimonial updated successfully.', 'testimonial' => $testimonial]);
    }

    public function testimonialsDestroy(Testimonial $testimonial)
    {
        $this->deleteFile($testimonial->image);
        $testimonial->delete();
        return response()->json(['status' => true, 'message' => 'Testimonial deleted successfully.']);
    }

    /* ------------------------------------------------------------------ */
    /* Pages                                                               */
    /* ------------------------------------------------------------------ */
    public function pages(Request $request)
    {
        $pages = Page::query()
            ->when($request->search, fn($q) => $q->where('title', 'like', "%$request->search%")->orWhere('slug', 'like', "%$request->search%"))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 10);
        $pages->getCollection()->transform(fn($p) => $p->setAttribute('banner_image_url', $this->storageUrl($p->banner_image)));
        return response()->json($pages);
    }

    public function pageShow(Page $page)
    {
        $page->setAttribute('banner_image_url', $this->storageUrl($page->banner_image));
        return response()->json(['status' => true, 'page' => $page]);
    }

    public function pagesStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:pages,slug',
            'description' => 'nullable',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'meta_title' => 'nullable|max:255',
            'meta_keywords' => 'nullable',
            'meta_description' => 'nullable',
            'status' => 'boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['status'] = $request->boolean('status', true);
        if ($request->hasFile('banner_image')) $data['banner_image'] = $this->uploadFile($request->file('banner_image'), 'pages');
        $page = Page::create($data);
        $page->setAttribute('banner_image_url', $this->storageUrl($page->banner_image));
        return response()->json(['status' => true, 'message' => 'Page created successfully.', 'page' => $page], 201);
    }

    public function pagesUpdate(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|max:255',
            'slug' => 'nullable|unique:pages,slug,' . $page->id,
            'description' => 'nullable',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'meta_title' => 'nullable|max:255',
            'meta_keywords' => 'nullable',
            'meta_description' => 'nullable',
            'status' => 'boolean',
        ]);
        if ($request->hasFile('banner_image')) {
            $this->deleteFile($page->banner_image);
            $data['banner_image'] = $this->uploadFile($request->file('banner_image'), 'pages');
        }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $page->update($data);
        $page->setAttribute('banner_image_url', $this->storageUrl($page->banner_image));
        return response()->json(['status' => true, 'message' => 'Page updated successfully.', 'page' => $page]);
    }

    public function pagesDestroy(Page $page)
    {
        $this->deleteFile($page->banner_image);
        $page->delete();
        return response()->json(['status' => true, 'message' => 'Page deleted successfully.']);
    }

    public function pagesToggle(Page $page)
    {
        $page->update(['status' => !$page->status]);
        return response()->json(['status' => true, 'message' => 'Page status updated.', 'page' => $page]);
    }

    /* ------------------------------------------------------------------ */
    /* Homepage Sections                                                   */
    /* ------------------------------------------------------------------ */
    public function homepageSections(Request $request)
    {
        $sections = HomepageSection::query()
            ->when($request->search, fn($q) => $q->where('title', 'like', "%$request->search%")->orWhere('section_key', 'like', "%$request->search%"))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->orderBy('sort_order')
            ->paginate($request->per_page ?? 10);
        $sections->getCollection()->transform(fn($s) => $s->setAttribute('image_url', $this->storageUrl($s->image))->setAttribute('background_image_url', $this->storageUrl($s->background_image)));
        return response()->json($sections);
    }

    public function homepageSectionShow(HomepageSection $section)
    {
        $section->setAttribute('image_url', $this->storageUrl($section->image));
        $section->setAttribute('background_image_url', $this->storageUrl($section->background_image));
        return response()->json(['status' => true, 'section' => $section]);
    }

    public function homepageSectionsStore(Request $request)
    {
        $data = $request->validate([
            'section_key' => 'required|unique:homepage_sections,section_key',
            'title' => 'required|max:255',
            'subtitle' => 'nullable|max:255',
            'description' => 'nullable',
            'button_text' => 'nullable|max:255',
            'button_url' => 'nullable|max:255',
            'icon' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'sort_order' => 'nullable|integer',
            'status' => 'required',
        ]);
        if ($request->hasFile('image')) $data['image'] = $this->uploadFile($request->file('image'), 'homepage');
        if ($request->hasFile('background_image')) $data['background_image'] = $this->uploadFile($request->file('background_image'), 'homepage');
        $section = HomepageSection::create($data);
        return response()->json(['status' => true, 'message' => 'Homepage section created successfully.', 'section' => $section], 201);
    }

    public function homepageSectionsUpdate(Request $request, HomepageSection $section)
    {
        $data = $request->validate([
            'section_key' => 'required|unique:homepage_sections,section_key,' . $section->id,
            'title' => 'required|max:255',
            'subtitle' => 'nullable|max:255',
            'description' => 'nullable',
            'button_text' => 'nullable|max:255',
            'button_url' => 'nullable|max:255',
            'icon' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'sort_order' => 'nullable|integer',
            'status' => 'required',
        ]);
        if ($request->hasFile('image')) { $this->deleteFile($section->image); $data['image'] = $this->uploadFile($request->file('image'), 'homepage'); }
        if ($request->hasFile('background_image')) { $this->deleteFile($section->background_image); $data['background_image'] = $this->uploadFile($request->file('background_image'), 'homepage'); }
        $section->update($data);
        return response()->json(['status' => true, 'message' => 'Homepage section updated successfully.', 'section' => $section]);
    }

    public function homepageSectionsDestroy(HomepageSection $section)
    {
        $this->deleteFile($section->image);
        $this->deleteFile($section->background_image);
        $section->delete();
        return response()->json(['status' => true, 'message' => 'Homepage section deleted successfully.']);
    }

    public function homepageSectionsToggle(HomepageSection $section)
    {
        $section->update(['status' => !$section->status]);
        return response()->json(['status' => true, 'message' => 'Section status updated.', 'section' => $section]);
    }

    /* ------------------------------------------------------------------ */
    /* SEO Settings                                                        */
    /* ------------------------------------------------------------------ */
    public function seoSettings(Request $request)
    {
        $seo = SeoSetting::query()
            ->when($request->search, fn($q) => $q->where('page_key', 'like', "%$request->search%")->orWhere('meta_title', 'like', "%$request->search%"))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->orderBy('page_key')
            ->paginate($request->per_page ?? 10);
        $seo->getCollection()->transform(function ($s) {
            $s->setAttribute('og_image_url', $this->storageUrl($s->og_image));
            $s->setAttribute('twitter_image_url', $this->storageUrl($s->twitter_image));
            return $s;
        });
        return response()->json($seo);
    }

    public function seoSettingShow(SeoSetting $seoSetting)
    {
        $seoSetting->setAttribute('og_image_url', $this->storageUrl($seoSetting->og_image));
        $seoSetting->setAttribute('twitter_image_url', $this->storageUrl($seoSetting->twitter_image));
        return response()->json(['status' => true, 'seo' => $seoSetting]);
    }

    public function seoSettingsStore(Request $request)
    {
        $data = $request->validate([
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
            'status' => 'boolean',
            'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'twitter_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data['status'] = $request->boolean('status', true);
        if ($request->hasFile('og_image')) $data['og_image'] = $this->uploadFile($request->file('og_image'), 'seo');
        if ($request->hasFile('twitter_image')) $data['twitter_image'] = $this->uploadFile($request->file('twitter_image'), 'seo');
        $item = SeoSetting::create($data);
        return response()->json(['status' => true, 'message' => 'SEO setting created successfully.', 'seo' => $item], 201);
    }

    public function seoSettingsUpdate(Request $request, SeoSetting $seoSetting)
    {
        $data = $request->validate([
            'page_key' => 'required|unique:seo_settings,page_key,' . $seoSetting->id,
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
            'canonical_url' => 'nullable',
            'robots' => 'nullable',
            'og_title' => 'nullable|max:255',
            'og_description' => 'nullable',
            'twitter_title' => 'nullable|max:255',
            'twitter_description' => 'nullable',
            'status' => 'boolean',
            'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'twitter_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($request->hasFile('og_image')) { $this->deleteFile($seoSetting->og_image); $data['og_image'] = $this->uploadFile($request->file('og_image'), 'seo'); }
        if ($request->hasFile('twitter_image')) { $this->deleteFile($seoSetting->twitter_image); $data['twitter_image'] = $this->uploadFile($request->file('twitter_image'), 'seo'); }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $seoSetting->update($data);
        return response()->json(['status' => true, 'message' => 'SEO setting updated successfully.', 'seo' => $seoSetting]);
    }

    public function seoSettingsDestroy(SeoSetting $seoSetting)
    {
        $this->deleteFile($seoSetting->og_image);
        $this->deleteFile($seoSetting->twitter_image);
        $seoSetting->delete();
        return response()->json(['status' => true, 'message' => 'SEO setting deleted successfully.']);
    }

    public function seoSettingsToggle(SeoSetting $seoSetting)
    {
        $seoSetting->update(['status' => !$seoSetting->status]);
        return response()->json(['status' => true, 'message' => 'SEO setting status updated.', 'seo' => $seoSetting]);
    }

    /* ------------------------------------------------------------------ */
    /* Maintenance                                                         */
    /* ------------------------------------------------------------------ */
    public function maintenance()
    {
        $m = MaintenanceSetting::first();
        if (!$m) {
            $m = MaintenanceSetting::create(['title' => 'Website Under Maintenance', 'status' => 0]);
        }
        $m->setAttribute('image_url', $this->storageUrl($m->image));
        return response()->json(['maintenance' => $m]);
    }

    public function maintenanceUpdate(Request $request)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|max:255',
            'message' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'button_text' => 'nullable|max:255',
            'button_url' => 'nullable|url',
            'status' => 'boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);
        $m = MaintenanceSetting::first();
        if (!$m) $m = new MaintenanceSetting();
        if ($request->hasFile('image')) { $this->deleteFile($m->image); $data['image'] = $this->uploadFile($request->file('image'), 'maintenance'); }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $m->fill($data)->save();
        $m->setAttribute('image_url', $this->storageUrl($m->image));
        return response()->json(['status' => true, 'message' => 'Maintenance settings updated successfully.', 'maintenance' => $m]);
    }
}

