<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Programming & Tech
            ['name' => 'Web Development', 'slug' => 'web-development', 'icon' => 'fas fa-code', 'status' => true],
            ['name' => 'Mobile App Development', 'slug' => 'mobile-app-development', 'icon' => 'fas fa-mobile-alt', 'status' => true],
            ['name' => 'Programming & Tech', 'slug' => 'programming-tech', 'icon' => 'fas fa-laptop-code', 'status' => true],
            ['name' => 'WordPress', 'slug' => 'wordpress', 'icon' => 'fab fa-wordpress', 'status' => true],
            ['name' => 'Software Development', 'slug' => 'software-development', 'icon' => 'fas fa-cogs', 'status' => true],
            ['name' => 'Website Builders & CMS', 'slug' => 'website-builders-cms', 'icon' => 'fas fa-th-large', 'status' => true],
            ['name' => 'Game Development', 'slug' => 'game-development', 'icon' => 'fas fa-gamepad', 'status' => true],
            ['name' => 'Support & IT', 'slug' => 'support-it', 'icon' => 'fas fa-headset', 'status' => true],
            ['name' => 'DevOps & Cloud', 'slug' => 'devops-cloud', 'icon' => 'fas fa-cloud', 'status' => true],

            // Design
            ['name' => 'Graphic Design', 'slug' => 'graphic-design', 'icon' => 'fas fa-paint-brush', 'status' => true],
            ['name' => 'Logo Design', 'slug' => 'logo-design', 'icon' => 'fas fa-pen-nib', 'status' => true],
            ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design', 'icon' => 'fas fa-palette', 'status' => true],
            ['name' => 'Illustration', 'slug' => 'illustration', 'icon' => 'fas fa-pencil-ruler', 'status' => true],
            ['name' => 'Brand Identity', 'slug' => 'brand-identity', 'icon' => 'fas fa-fingerprint', 'status' => true],
            ['name' => 'Architecture & Interior Design', 'slug' => 'architecture-interior-design', 'icon' => 'fas fa-building', 'status' => true],
            ['name' => 'Product Design', 'slug' => 'product-design', 'icon' => 'fas fa-box-open', 'status' => true],
            ['name' => 'Web Design', 'slug' => 'web-design', 'icon' => 'fas fa-window-restore', 'status' => true],

            // Digital Marketing
            ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'icon' => 'fas fa-bullhorn', 'status' => true],
            ['name' => 'Social Media Marketing', 'slug' => 'social-media-marketing', 'icon' => 'fas fa-share-alt', 'status' => true],
            ['name' => 'SEO', 'slug' => 'seo', 'icon' => 'fas fa-search', 'status' => true],
            ['name' => 'Search Advertising', 'slug' => 'search-advertising', 'icon' => 'fas fa-ad', 'status' => true],
            ['name' => 'Email Marketing', 'slug' => 'email-marketing', 'icon' => 'fas fa-envelope-open-text', 'status' => true],
            ['name' => 'Content Marketing', 'slug' => 'content-marketing', 'icon' => 'fas fa-newspaper', 'status' => true],
            ['name' => 'Social Media Advertising', 'slug' => 'social-media-advertising', 'icon' => 'fas fa-adjust', 'status' => true],
            ['name' => 'Influencer Marketing', 'slug' => 'influencer-marketing', 'icon' => 'fas fa-user-tie', 'status' => true],
            ['name' => 'Analytics & Tracking', 'slug' => 'analytics-tracking', 'icon' => 'fas fa-chart-pie', 'status' => true],

            // Writing & Translation
            ['name' => 'Content Writing', 'slug' => 'content-writing', 'icon' => 'fas fa-pen-fancy', 'status' => true],
            ['name' => 'Writing & Translation', 'slug' => 'writing-translation', 'icon' => 'fas fa-language', 'status' => true],
            ['name' => 'Copywriting', 'slug' => 'copywriting', 'icon' => 'fas fa-feather-alt', 'status' => true],
            ['name' => 'Translation & Transcription', 'slug' => 'translation-transcription', 'icon' => 'fas fa-file-alt', 'status' => true],
            ['name' => 'Resume & Cover Letters', 'slug' => 'resume-cover-letters', 'icon' => 'fas fa-file-pdf', 'status' => true],
            ['name' => 'Creative Writing', 'slug' => 'creative-writing', 'icon' => 'fas fa-book', 'status' => true],

            // Video & Animation
            ['name' => 'Video Editing', 'slug' => 'video-editing', 'icon' => 'fas fa-video', 'status' => true],
            ['name' => 'Video & Animation', 'slug' => 'video-animation', 'icon' => 'fas fa-film', 'status' => true],
            ['name' => 'Animation', 'slug' => 'animation', 'icon' => 'fas fa-spinner', 'status' => true],
            ['name' => 'Motion Graphics', 'slug' => 'motion-graphics', 'icon' => 'fas fa-wand-magic', 'status' => true],

            // Music & Audio
            ['name' => 'Music & Audio', 'slug' => 'music-audio', 'icon' => 'fas fa-music', 'status' => true],
            ['name' => 'Voice Over', 'slug' => 'voice-over', 'icon' => 'fas fa-microphone', 'status' => true],
            ['name' => 'Audio Production', 'slug' => 'audio-production', 'icon' => 'fas fa-headphones', 'status' => true],

            // AI & Data
            ['name' => 'AI Services', 'slug' => 'ai-services', 'icon' => 'fas fa-brain', 'status' => true],
            ['name' => 'Data Science', 'slug' => 'data-science', 'icon' => 'fas fa-chart-line', 'status' => true],
            ['name' => 'Data Entry', 'slug' => 'data-entry', 'icon' => 'fas fa-keyboard', 'status' => true],
            ['name' => 'Data Analysis', 'slug' => 'data-analysis', 'icon' => 'fas fa-chart-bar', 'status' => true],
            ['name' => 'Machine Learning', 'slug' => 'machine-learning', 'icon' => 'fas fa-robot', 'status' => true],

            // Business
            ['name' => 'Business Consulting', 'slug' => 'business-consulting', 'icon' => 'fas fa-briefcase', 'status' => true],
            ['name' => 'Finance & Accounting', 'slug' => 'finance-accounting', 'icon' => 'fas fa-calculator', 'status' => true],
            ['name' => 'Legal Consulting', 'slug' => 'legal-consulting', 'icon' => 'fas fa-gavel', 'status' => true],
            ['name' => 'Virtual Assistant', 'slug' => 'virtual-assistant', 'icon' => 'fas fa-user-clock', 'status' => true],
            ['name' => 'Project Management', 'slug' => 'project-management', 'icon' => 'fas fa-tasks', 'status' => true],

            // Photography & Lifestyle
            ['name' => 'Photography', 'slug' => 'photography', 'icon' => 'fas fa-camera', 'status' => true],
            ['name' => 'Photo Editing', 'slug' => 'photo-editing', 'icon' => 'fas fa-images', 'status' => true],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'icon' => 'fas fa-heart', 'status' => true],

            // Admin & Support
            ['name' => 'Admin & Customer Support', 'slug' => 'admin-customer-support', 'icon' => 'fas fa-user-headset', 'status' => true],
            ['name' => 'E-commerce', 'slug' => 'ecommerce', 'icon' => 'fas fa-shopping-cart', 'status' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
