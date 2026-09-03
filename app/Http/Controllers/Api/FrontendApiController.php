<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\HomepageSection;
use App\Models\Banner;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Setting;
use App\Models\Tutorial;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class FrontendApiController extends Controller
{
    private function storageUrl($path)
    {
        return $path ? url('storage/' . $path) : null;
    }

    /* ------------------------------------------------------------------ */
    /* Home                                                               */
    /* ------------------------------------------------------------------ */
    public function home(): JsonResponse
    {
        $sections = HomepageSection::where('status', true)
            ->orderBy('sort_order')
            ->get();

        $banners = Banner::where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($b) {
                $b->setAttribute('image_url', $this->storageUrl($b->image));
                return $b;
            });

        $topServices = Service::with(['seller', 'category', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($s) => $this->serializeServiceCard($s));

        return response()->json([
            'sections' => $sections,
            'banners' => $banners,
            'top_services' => $topServices,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Categories                                                         */
    /* ------------------------------------------------------------------ */
    public function categories(): JsonResponse
    {
        $categories = Category::withCount('services')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /* ------------------------------------------------------------------ */
    /* Navigation (public header menu)                                    */
    /* ------------------------------------------------------------------ */
    public function navigation(): JsonResponse
    {
        $defaults = $this->defaultNavigation();

        $items = Setting::where('group', 'frontend')->pluck('value', 'key');
        if ($items->get('nav_overrides')) {
            try {
                $overrides = json_decode($items->get('nav_overrides'), true);
                if (is_array($overrides) && count($overrides)) {
                    return response()->json($overrides);
                }
            } catch (\Throwable $e) {
                // fall through to defaults
            }
        }

        return response()->json($defaults);
    }

    private function defaultNavigation(): array
    {
        return [
            ['id' => 1, 'title' => 'Home',     'url' => '/',               'visible' => true, 'order' => 1, 'icon' => null],
            ['id' => 2, 'title' => 'About',    'url' => '/about',          'visible' => true, 'order' => 2, 'icon' => null],
            ['id' => 3, 'title' => 'Services', 'url' => '/category/web-development', 'visible' => true, 'order' => 3, 'icon' => null],
            ['id' => 4, 'title' => 'Pricing',  'url' => '/pricing',        'visible' => true, 'order' => 4, 'icon' => null],
            ['id' => 5, 'title' => 'Blog',     'url' => '/blog',           'visible' => true, 'order' => 5, 'icon' => null],
            ['id' => 6, 'title' => 'FAQ',      'url' => '/faq',            'visible' => true, 'order' => 6, 'icon' => null],
            ['id' => 7, 'title' => 'Tutorials', 'url' => '/tutorials',     'visible' => true, 'order' => 7, 'icon' => null],
            ['id' => 8, 'title' => 'Contact',  'url' => '/contact',        'visible' => true, 'order' => 8, 'icon' => null],
            ['id' => 9, 'title' => 'Careers',  'url' => '/careers',        'visible' => true, 'order' => 9, 'icon' => null],
            ['id' => 10, 'title' => 'App',     'url' => '/app',            'visible' => true, 'order' => 10, 'icon' => null],
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Footer                                                             */
    /* ------------------------------------------------------------------ */
    public function footer(): JsonResponse
    {
        $settings = Setting::where('group', 'frontend')->pluck('value', 'key');

        $socialDefaults = [
            ['id' => 1, 'icon' => 'facebook',  'url' => '#', 'visible' => true],
            ['id' => 2, 'icon' => 'instagram', 'url' => '#', 'visible' => true],
            ['id' => 3, 'icon' => 'linkedin',  'url' => '#', 'visible' => true],
            ['id' => 4, 'icon' => 'twitter',   'url' => '#', 'visible' => true],
        ];

        $sections = [
            [
                'id' => 1,
                'title' => 'Company',
                'links' => [
                    ['label' => 'About',        'url' => '/about'],
                    ['label' => 'Careers',      'url' => '/careers'],
                    ['label' => 'Blog',         'url' => '/blog'],
                    ['label' => 'Contact',      'url' => '/contact'],
                    ['label' => 'Pricing',      'url' => '/pricing'],
                ],
            ],
            [
                'id' => 2,
                'title' => 'Marketplace',
                'links' => [
                    ['label' => 'Find Services', 'url' => '/category/web-development'],
                    ['label' => 'Web Development', 'url' => '/category/web-development'],
                    ['label' => 'Graphic Design',  'url' => '/category/graphic-design'],
                    ['label' => 'AI Services',     'url' => '/category/ai-services'],
                    ['label' => 'FAQ',             'url' => '/faq'],
                ],
            ],
            [
                'id' => 3,
                'title' => 'Support',
                'links' => [
                    ['label' => 'Contact Us',    'url' => '/contact'],
                    ['label' => 'Privacy Policy','url' => '/privacy-policy'],
                    ['label' => 'Terms',         'url' => '/terms-conditions'],
                    ['label' => 'Cookies',       'url' => '/cookie-policy'],
                    ['label' => 'FAQs',          'url' => '/faq'],
                ],
            ],
        ];

        // Allow admins to override footer sections via a JSON setting.
        if ($settings->get('footer_sections')) {
            try {
                $override = json_decode($settings->get('footer_sections'), true);
                if (is_array($override) && count($override)) {
                    $sections = $override;
                }
            } catch (\Throwable $e) {
                // keep defaults
            }
        }

        return response()->json([
            'company' => [
                'logo'        => $settings->get('site_name') ?: 'KhanVerse',
                'description' => $settings->get('footer_text')
                    ?: 'The next generation freelance marketplace powered by AI.',
                'social'      => $socialDefaults,
            ],
            'sections'  => $sections,
            'copyright' => '© ' . date('Y') . ' ' . ($settings->get('site_name') ?: 'KhanVerse') . '. All Rights Reserved.',
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* FAQs                                                               */
    /* ------------------------------------------------------------------ */
    public function faqs(): JsonResponse
    {
        $faqs = Faq::where('status', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($faqs);
    }

    /* ------------------------------------------------------------------ */
    /* Testimonials                                                       */
    /* ------------------------------------------------------------------ */
    public function testimonials(): JsonResponse
    {
        $testimonials = Testimonial::where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($t) {
                $t->setAttribute('image_url', $this->storageUrl($t->image));
                return $t;
            });

        return response()->json($testimonials);
    }

    /* ------------------------------------------------------------------ */
    /* Tutorials (public)                                                 */
    /* ------------------------------------------------------------------ */
    public function tutorials(): JsonResponse
    {
        $tutorials = Tutorial::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json($tutorials);
    }

    /* ------------------------------------------------------------------ */
    /* App download settings (public)                                     */
    /* ------------------------------------------------------------------ */
    public function appSettings(): JsonResponse
    {
        $downloadUrl = Setting::where('group', 'app')->where('key', 'download_url')->value('value');

        return response()->json([
            'download_url' => $downloadUrl ?: '/app/khanverse.apk',
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Category -> services listing                                       */
    /* ------------------------------------------------------------------ */
    public function categoryServices(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->withCount('services')
            ->where('status', true)
            ->first();

        if (!$category) {
            return response()->json([
                'category' => ['name' => ucwords(str_replace('-', ' ', $slug)), 'slug' => $slug, 'services_count' => 0],
                'services' => [],
            ]);
        }

        $services = $category->services()
            ->with(['seller', 'category', 'images'])
            ->where('status', 'active')
            ->latest()
            ->get()
            ->map(fn($s) => $this->serializeServiceCard($s));

        return response()->json([
            'category' => [
                'id'             => $category->id,
                'name'           => $category->name,
                'slug'           => $category->slug,
                'services_count' => $category->services_count,
            ],
            'services' => $services,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Service detail                                                     */
    /* ------------------------------------------------------------------ */
    public function service(int $id): JsonResponse
    {
        $service = Service::with(['seller.user', 'category', 'images'])
            ->where('status', 'active')
            ->find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        $similar = Service::with(['seller', 'category', 'images'])
            ->where('status', 'active')
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($s) => $this->serializeServiceCard($s));

        $images = $service->images->map(fn($img) => $this->storageUrl($img->image))->filter()->values();

        $seller = $service->seller;
        $sellerData = $seller ? [
            'id'               => $seller->id,
            'full_name'        => $seller->full_name,
            'experience_level' => $seller->experience_level,
            'avatar'           => $this->storageUrl($seller->profile_image),
            'rating'           => $seller->rating ?? null,
            'total_orders'     => $seller->total_earning ? null : null,
            'country'          => $seller->country,
        ] : null;

        return response()->json([
            'service' => [
                'id'            => $service->id,
                'title'         => $service->title,
                'slug'          => $service->slug,
                'description'   => $service->description,
                'thumbnail'     => $this->storageUrl($service->thumbnail),
                'images'        => $images->all(),
                'price'         => (float)$service->price,
                'delivery_days' => $service->delivery_days,
                'revisions'     => $service->revisions,
                'reviews_count' => 0,
                'rating'        => 4.8,
                'category'      => $service->category ? [
                    'name' => $service->category->name,
                    'slug' => $service->category->slug,
                ] : null,
                'seller'        => $sellerData,
                'similar_services' => $similar,
            ],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Serializer: compact service card for grids                          */
    /* ------------------------------------------------------------------ */
    private function serializeServiceCard(Service $service): array
    {
        $seller = $service->seller;
        $thumbnail = $service->images->first()->image ?? $service->thumbnail;

        return [
            'id'            => $service->id,
            'title'         => $service->title,
            'slug'          => $service->slug,
            'price'         => (float)$service->price,
            'delivery_days' => $service->delivery_days,
            'revisions'     => $service->revisions,
            'thumbnail'     => $this->storageUrl($thumbnail),
            'image'         => $this->storageUrl($thumbnail),
            'rating'        => 4.8,
            'reviews'       => 0,
            'sold'          => 0,
            'level'         => $seller?->experience_level ?: 'New Seller',
            'seller' => $seller ? [
                'id'               => $seller->id,
                'full_name'        => $seller->full_name,
                'name'             => $seller->full_name,
                'experience_level' => $seller->experience_level,
            ] : null,
            'category' => $service->category ? [
                'id'   => $service->category->id,
                'name' => $service->category->name,
                'slug' => $service->category->slug,
            ] : null,
        ];
    }
}