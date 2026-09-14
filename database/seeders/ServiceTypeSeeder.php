<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\ServiceType;
use Illuminate\Support\Str;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // ---- Non-technical / home services ----
            'Carpentry & Furniture' => [
                'Carpenter', 'Furniture Assembly', 'Cabinet Making', 'Wood Repair & Polishing',
                'Modular Kitchen Installation', 'Door & Window Repair', 'Custom Furniture Design',
            ],
            'Painting & Renovation' => [
                'Painter', 'Interior Wall Painting', 'Exterior Painting', 'Texture Painting',
                'Waterproofing', 'Tiling & Grouting', 'Bathroom Renovation', 'False Ceiling Installation',
            ],
            'Plumbing' => [
                'Plumber', 'Tap & Pipe Repair', 'Bathroom Fitting', 'Water Heater Installation',
                'Leak Detection & Repair', 'Drainage & Septic Cleaning', 'Pump Installation',
            ],
            'Masonry & Construction' => [
                'Mistri', 'Mason', 'RCC & Brick Work', 'Plastering', 'Flooring & Skirting',
                'Boundary Wall Construction', 'House Extension', 'Demolition & Debris Removal',
            ],
            'Electrical Services' => [
                'Electrician', 'Wiring & Rewiring', 'Switch & Socket Installation', 'Lighting Installation',
                'MCB & Distribution Board Upgrade', 'Fan Installation & Repair', 'Home Automation Setup',
            ],
            'Home Cleaning & Pest Control' => [
                'Deep Cleaning', 'Bathroom & Kitchen Cleaning', 'Sofa & Carpet Cleaning',
                'Pest Control', 'Termite Treatment', 'Water Tank Cleaning', 'Move-in/Move-out Cleaning',
            ],
            'Appliance & Repair Services' => [
                'AC Service & Repair', 'Refrigerator Repair', 'Washing Machine Repair',
                'Microwave Repair', 'TV Repair', 'RO & Water Purifier Service',
            ],
            'Moving & Packing' => [
                'House Shifting', 'Office Shifting', 'Vehicle Shifting', 'Packers & Movers',
                'Storage & Warehousing', 'Item Wrapping & Crating',
            ],
            'Gardening & Landscaping' => [
                'Gardener', 'Lawn Mowing', 'Garden Landscaping', 'Planting & Tree Care',
                'Drip Irrigation Setup', 'Balcony & Terrace Gardening',
            ],
        ];

        foreach ($types as $categoryName => $serviceNames) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'icon' => 'fas fa-tools',
                    'status' => true,
                ]
            );

            foreach ($serviceNames as $name) {
                ServiceType::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'icon' => 'fas fa-tools',
                        'is_active' => true,
                    ]
                );
            }
        }

        // ---- Technical / digital service types attached to existing categories ----
        $techTypes = [
            'web-development' => [
                'Website Development', 'E-commerce Website', 'Landing Page Design', 'Website Maintenance',
                'Full-Stack Web App', 'API Development & Integration', 'Web Portal Development',
            ],
            'mobile-app-development' => [
                'Android App Development', 'iOS App Development', 'Cross-Platform App', 'App UI/UX Design',
                'App Maintenance & Support', 'Flutter Development', 'React Native Development',
            ],
            'graphic-design' => [
                'Logo Design', 'Business Card Design', 'Social Media Posts', 'Brochure & Flyer Design',
                'Brand Identity Design', 'Packaging Design', 'Poster Design',
            ],
            'ui-ux-design' => [
                'Website UI Design', 'Mobile App UI Design', 'UX Research', 'Wireframing & Prototyping',
                'Design System Creation', 'Landing Page UI',
            ],
            'seo' => [
                'On-Page SEO', 'Off-Page SEO', 'Technical SEO', 'Local SEO', 'SEO Audit & Consulting',
                'Keyword Research', 'Link Building',
            ],
            'digital-marketing' => [
                'Social Media Management', 'Google Ads Campaign', 'Meta (Facebook) Ads', 'Email Marketing',
                'Content Marketing', 'Influencer Marketing', 'Marketing Funnel Setup',
            ],
            'content-writing' => [
                'Article Writing', 'Blog Post Writing', 'Website Copywriting', 'Product Description Writing',
                'SEO Content Writing', 'Press Release Writing', 'Script Writing',
            ],
            'video-editing' => [
                'YouTube Video Editing', 'Short-form Editing', 'Gaming Video Editing', 'Vlog Editing',
                'Color Grading', 'Cinematic Editing', 'Motion Graphics',
            ],
            'ai-services' => [
                'Chatbot Development', 'LLM Integration', 'AI Automation Workflows', 'Custom AI Model Training',
                'Prompt Engineering', 'AI Content Generation', 'Computer Vision Solutions',
            ],
            'data-entry' => [
                'Form Filling', 'Copy Paste Services', 'Image Data Entry', 'PDF to Excel Conversion',
                'Web Research', 'Data Cleaning',
            ],
            'voice-over' => [
                'Commercial Voice Over', 'Narration', 'Podcast & IVR Voice', 'Character Voice Over',
                'English Voice Over', 'Hindi Voice Over',
            ],
            'virtual-assistant' => [
                'Admin & Data Assistance', 'Email & Calendar Management', 'Customer Support Assistant',
                'Order Processing', 'Research Assistant', 'Social Media Assistant',
            ],
            'wordpress' => [
                'WordPress Site Setup', 'Custom Theme Development', 'Plugin Development & Fixes',
                'WooCommerce Store', 'WordPress Speed Optimization', 'Elementor Design',
            ],
        ];

        foreach ($techTypes as $categorySlug => $serviceNames) {
            $category = Category::where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }

            foreach ($serviceNames as $name) {
                ServiceType::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'icon' => 'fas fa-layer-group',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}