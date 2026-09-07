<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Seller;
use App\Models\Buyer;
use App\Models\Order;
use App\Models\Review;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\HomepageSection;
use App\Models\Banner;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Setting;
use App\Models\Tutorial;
use App\Models\BrandPartner;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['orders as completed_sales' => fn($q) => $q->where('status', 'completed')])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($s) => $this->serializeServiceCard($s));

        $sellerCountries = Seller::whereNotNull('country')->where('country', '!=', '')->pluck('country');
        $buyerCountries = Buyer::whereNotNull('country')->where('country', '!=', '')->pluck('country');

        $stats = [
            'services'    => Service::where('status', 'active')->count(),
            'sellers'     => Seller::whereHas('services', fn($q) => $q->where('status', 'active'))->count(),
            'buyers'      => Buyer::count(),
            'categories'  => Category::where('status', true)->count(),
            'projects'    => Order::count(),
            'delivered'   => Order::where('status', 'completed')->count(),
            'countries'   => count(array_unique(array_merge($sellerCountries->all(), $buyerCountries->all()))),
            'rating'      => round((float)(Review::avg('rating') ?? 0), 1),
            'reviews'     => Review::count(),
        ];

        return response()->json([
            'sections' => $sections,
            'banners' => $banners,
            'top_services' => $topServices,
            'stats' => $stats,
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
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Team members (founders & leadership, public)                        */
    /* ------------------------------------------------------------------ */
    public function team(): JsonResponse
    {
        $members = TeamMember::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'role', 'tagline', 'image']);

        return response()->json($members);
    }

    /* ------------------------------------------------------------------ */
    /* App Install Prompt                                                  */
    /* ------------------------------------------------------------------ */
    public function appInstall(): JsonResponse
    {
        $settings = Setting::where('group', 'app')->pluck('value', 'key');

        $enabled = ($settings->get('install_prompt_enabled') ?? '1') !== '0';

        $rawBuilds = json_decode((string) $settings->get('apk_builds', '[]'), true);
        $builds = is_array($rawBuilds) ? $rawBuilds : [];
        $active = null;

        foreach ($builds as $build) {
            if (!empty($build['active'])) {
                $active = $build;
                break;
            }
        }

        if (!$active && count($builds) > 0) {
            $active = $builds[0];
        }

        return response()->json([
            'enabled' => (bool) $enabled,
            'apk' => $active ? [
                'url'      => $active['url'] ?? null,
                'version'  => $active['version'] ?? null,
                'size'     => $active['size'] ?? null,
                'filename' => $active['filename'] ?? null,
            ] : null,
        ]);
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
                    ['label' => 'Tutorials', 'url' => '/tutorials'],
                    ['label' => 'Web Development', 'url' => '/category/web-development'],
                    ['label' => 'Graphic Design',  'url' => '/category/graphic-design'],
                    ['label' => 'AI Services',     'url' => '/category/ai-services'],
                    ['label' => 'FAQ',             'url' => '/faq'],
                    ['label' => 'Download App',    'url' => '/app'],
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
            [
                'id' => 4,
                'title' => 'Account',
                'links' => [
                    ['label' => 'Sign In',        'url' => '/login'],
                    ['label' => 'Create Account', 'url' => '/register'],
                    ['label' => 'Download App',   'url' => '/app'],
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
            'sub_brand' => $this->subBrand($settings),
            'copyright' => '© ' . date('Y') . ' ' . ($settings->get('site_name') ?: 'KhanVerse') . '. All Rights Reserved.',
        ]);
    }

    private function subBrand($settings = null): array
    {
        $settings = $settings ?: Setting::where('group', 'frontend')->pluck('value', 'key');

        return [
            'name'         => $settings->get('sub_brand_name') ?: 'AMTech',
            'since'        => $settings->get('sub_brand_since') ?: '2014',
            'tagline'      => $settings->get('sub_brand_tagline') ?: 'Part of the AMTech family',
            'description'  => $settings->get('sub_brand_text') ?: 'KhanVerse is a product of AMTech — a trusted technology company building digital solutions for over a decade.',
            'badge'        => $settings->get('sub_brand_badge') ?: '12+ Years of Trust',
            'url'          => $settings->get('sub_brand_url') ?: 'https://amtech.com',
        ];
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
    /* Brand partners (public trusted-by marquee)                          */
    /* ------------------------------------------------------------------ */
    public function trustedCompanies(): JsonResponse
    {
        $partners = BrandPartner::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'logo', 'url']);

        return response()->json($partners);
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
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['orders as completed_sales' => fn($q) => $q->where('status', 'completed')])
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
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['orders as completed_sales' => fn($q) => $q->where('status', 'completed')])
            ->where('status', 'active')
            ->find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        $similar = Service::with(['seller', 'category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['orders as completed_sales' => fn($q) => $q->where('status', 'completed')])
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
            'total_orders'     => Order::whereHas('service', fn($q) => $q->where('seller_id', $seller->id))->count(),
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
                'reviews_count' => (int)($service->reviews_count ?? 0),
                'rating'        => $service->reviews_avg_rating ? round((float)$service->reviews_avg_rating, 1) : null,
                'total_sales'   => (int)($service->completed_sales ?? 0),
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
    /* Search (public service search with filters)                        */
    /* ------------------------------------------------------------------ */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string)$request->query('q'));
        $categoryId = $request->integer('category_id') ?: null;
        $min = $request->input('min_price') !== '' && $request->input('min_price') !== null ? $request->float('min_price') : null;
        $max = $request->input('max_price') !== '' && $request->input('max_price') !== null ? $request->float('max_price') : null;
        $sort = in_array($request->query('sort'), ['latest', 'price_asc', 'price_desc', 'rating']) ? $request->query('sort') : 'latest';

        $query = Service::with(['seller', 'category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['orders as completed_sales' => fn($q) => $q->where('status', 'completed')])
            ->where('status', 'active');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
        }

        $services = $query->paginate($request->integer('per_page', 12))
            ->withQueryString();

        $services->setCollection(
            $services->getCollection()->map(fn($s) => $this->serializeServiceCard($s))
        );

        return response()->json([
            'q'         => $q,
            'categories' => Category::where('status', true)->orderBy('name')->get(['id', 'name', 'slug']),
            'services'   => $services,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Compare (service side-by-side comparison)                          */
    /* ------------------------------------------------------------------ */
    public function compare(Request $request): JsonResponse
    {
        $ids = collect(explode(',', (string)$request->query('ids', '')))
            ->map(fn($v) => (int)trim($v))
            ->filter()
            ->unique()
            ->take(4)
            ->values();

        $services = Service::with(['seller', 'category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['orders as completed_sales' => fn($q) => $q->where('status', 'completed')])
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get()
            ->map(fn($s) => $this->serializeServiceCard($s))
            ->sortBy(fn($s) => array_search($s['id'], $ids->all()))
            ->values();

        return response()->json(['services' => $services]);
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
            'rating'        => round((float)($service->reviews_avg_rating ?? 0), 1),
            'reviews'       => (int)($service->reviews_count ?? 0),
            'sold'          => (int)($service->completed_sales ?? 0),
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