<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Seller;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::first();
        if (!$seller) {
            $this->command->warn('No seller found. Skipping ServiceSeeder.');
            return;
        }

        $bySlug = Category::pluck('id', 'slug');

        $services = [
            [
                'title' => 'Modern Business Website Development',
                'slug' => 'modern-business-website-development',
                'category' => 'web-development',
                'description' => 'I will design and develop a stunning, fully responsive business website that reflects your brand and converts visitors into customers. Includes clean UI/UX, mobile optimization and SEO-ready structure.',
                'price' => 15000,
                'delivery_days' => 3,
                'delivery_method' => 'digital',
                'revisions' => 3,
            ],
            [
                'title' => 'E-commerce Store Setup & Configuration',
                'slug' => 'ecommerce-store-setup',
                'category' => 'web-development',
                'description' => 'Complete online store setup with product catalog, cart, secure checkout and payment gateway integration. Ready to start selling fast.',
                'price' => 25000,
                'delivery_days' => 7,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'Professional Logo Design & Branding Kit',
                'slug' => 'professional-logo-design-branding',
                'category' => 'graphic-design',
                'description' => 'A complete brand identity package including logo, color palette, typography and brand guidelines to make your business memorable.',
                'price' => 2500,
                'delivery_days' => 2,
                'delivery_method' => 'digital',
                'revisions' => 4,
            ],
            [
                'title' => 'UI/UX Design for Web & Mobile Apps',
                'slug' => 'ui-ux-design-web-mobile',
                'category' => 'ui-ux-design',
                'description' => 'Modern, user-friendly UI/UX design for websites and applications. Includes wireframes, prototypes and Figma source files.',
                'price' => 12000,
                'delivery_days' => 5,
                'delivery_method' => 'digital',
                'revisions' => 3,
            ],
            [
                'title' => 'Social Media Marketing & Management',
                'slug' => 'social-media-marketing-management',
                'category' => 'digital-marketing',
                'description' => 'Grow your brand with a complete social media strategy, content calendar, posting and monthly performance reporting.',
                'price' => 8000,
                'delivery_days' => 30,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'Professional SEO Audit & On-Page Optimization',
                'slug' => 'professional-seo-audit-onpage',
                'category' => 'seo',
                'description' => 'Boost your search rankings with a full technical SEO audit, keyword research and on-page optimization for your website.',
                'price' => 10000,
                'delivery_days' => 7,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'YouTube & Social Video Editing',
                'slug' => 'youtube-social-video-editing',
                'category' => 'video-editing',
                'description' => 'Professional video editing with captions, color grading, motion graphics and sound design. Perfect for YouTube and social media.',
                'price' => 5000,
                'delivery_days' => 3,
                'delivery_method' => 'digital',
                'revisions' => 3,
            ],
            [
                'title' => 'Native iOS & Android App Development',
                'slug' => 'native-ios-android-app-development',
                'category' => 'mobile-app-development',
                'description' => 'End-to-end mobile app development for Android and iOS with a clean architecture, push notifications and store deployment.',
                'price' => 45000,
                'delivery_days' => 15,
                'delivery_method' => 'digital',
                'revisions' => 3,
            ],
            [
                'title' => 'Content Writing & Blog Articles',
                'slug' => 'content-writing-blog-articles',
                'category' => 'content-writing',
                'description' => 'SEO-friendly, engaging articles and blog posts tailored to your audience and written in your brand voice.',
                'price' => 1500,
                'delivery_days' => 2,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'Data Analysis & Interactive Dashboards',
                'slug' => 'data-analysis-interactive-dashboards',
                'category' => 'data-science',
                'description' => 'Turn raw data into actionable insights with interactive dashboards, reports and predictive models.',
                'price' => 18000,
                'delivery_days' => 10,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'DevOps Setup: CI/CD & Cloud Deployment',
                'slug' => 'devops-setup-ci-cd-cloud',
                'category' => 'devops',
                'description' => 'Set up automated CI/CD pipelines, cloud infrastructure, monitoring and deployment for your application on AWS or DigitalOcean.',
                'price' => 30000,
                'delivery_days' => 10,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'Photo Retouching & Background Removal',
                'slug' => 'photo-retouching-background-removal',
                'category' => 'photography',
                'description' => 'Professional photo retouching, background removal and color correction to make your images look flawless for any use.',
                'price' => 800,
                'delivery_days' => 1,
                'delivery_method' => 'digital',
                'revisions' => 3,
            ],
            [
                'title' => 'Background Music & Audio Mixing',
                'slug' => 'background-music-audio-mixing',
                'category' => 'music-audio',
                'description' => 'Custom background music, audio mixing and mastering for podcasts, videos and commercial projects.',
                'price' => 3500,
                'delivery_days' => 3,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'AI Chatbot Integration & Automation',
                'slug' => 'ai-chatbot-integration-automation',
                'category' => 'ai-services',
                'description' => 'Set up an AI-powered chatbot or workflow automation for your business to save time and engage customers 24/7.',
                'price' => 9000,
                'delivery_days' => 5,
                'delivery_method' => 'digital',
                'revisions' => 2,
            ],
            [
                'title' => 'Business Plan & Pitch Deck Design',
                'slug' => 'business-plan-pitch-deck-design',
                'category' => 'business-consulting',
                'description' => 'A polished business plan or investor-ready pitch deck that clearly communicates your vision and growth strategy.',
                'price' => 7000,
                'delivery_days' => 4,
                'delivery_method' => 'digital',
                'revisions' => 3,
            ],
        ];

        foreach ($services as $s) {
            $categoryId = $bySlug->get($s['category']);
            Service::firstOrCreate(
                ['slug' => $s['slug']],
                [
                    'title' => $s['title'],
                    'description' => $s['description'],
                    'price' => $s['price'],
                    'delivery_days' => $s['delivery_days'],
                    'delivery_method' => $s['delivery_method'],
                    'revisions' => $s['revisions'],
                    'status' => 'active',
                    'seller_id' => $seller->id,
                    'category_id' => $categoryId,
                ]
            );
        }
    }
}
