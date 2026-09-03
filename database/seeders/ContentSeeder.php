<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\HomepageSection;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['question' => 'How does KhanVerse work?', 'answer' => 'KhanVerse connects freelancers (sellers) with clients (buyers). Sellers offer services, buyers purchase them, and projects are managed through our platform.', 'sort_order' => 1, 'status' => true],
            ['question' => 'How do I become a seller?', 'answer' => 'Register an account, complete your profile, and start listing your services. Your profile will be reviewed before going live.', 'sort_order' => 2, 'status' => true],
            ['question' => 'What are the fees?', 'answer' => 'KhanVerse charges a platform fee of 10% on each completed transaction. There are no upfront costs.', 'sort_order' => 3, 'status' => true],
            ['question' => 'How do payments work?', 'answer' => 'Buyers pay upfront when placing an order. Funds are held in escrow and released to the seller upon order completion and approval.', 'sort_order' => 4, 'status' => true],
            ['question' => 'Can I get a refund?', 'answer' => 'If you are not satisfied with the delivered work, you can request a revision or dispute the order. Our support team will help resolve the issue.', 'sort_order' => 5, 'status' => true],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }

        $testimonials = [
            ['name' => 'John Smith', 'designation' => 'CEO', 'company' => 'TechCorp', 'rating' => 5, 'review' => 'KhanVerse made it incredibly easy to find talented developers. Highly recommended!', 'sort_order' => 1, 'status' => true],
            ['name' => 'Sarah Johnson', 'designation' => 'Marketing Manager', 'company' => 'GrowthCo', 'rating' => 5, 'review' => 'As a freelancer, KhanVerse has helped me grow my client base significantly.', 'sort_order' => 2, 'status' => true],
            ['name' => 'Michael Chen', 'designation' => 'CTO', 'company' => 'StartupXYZ', 'rating' => 4, 'review' => 'Great platform with a smooth workflow. The escrow system gives me peace of mind.', 'sort_order' => 3, 'status' => true],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::firstOrCreate(
                ['name' => $testimonial['name']],
                $testimonial
            );
        }

        $sections = [
            ['section_key' => 'hero', 'title' => 'Find the Perfect Freelancer', 'subtitle' => 'KhanVerse', 'description' => 'Connect with talented professionals and get your project done.', 'button_text' => 'Get Started', 'button_url' => '/register', 'sort_order' => 1, 'status' => true],
            ['section_key' => 'why_choose_us', 'title' => 'How It Works', 'subtitle' => 'Simple Steps', 'description' => 'Post a project, choose a freelancer, and get your work done.', 'button_text' => null, 'button_url' => null, 'sort_order' => 2, 'status' => true],
            ['section_key' => 'featured_categories', 'title' => 'Browse Categories', 'subtitle' => 'Popular Services', 'description' => 'Find services across multiple categories.', 'button_text' => 'View All', 'button_url' => '/categories', 'sort_order' => 3, 'status' => true],
            ['section_key' => 'cta', 'title' => 'Ready to Get Started?', 'subtitle' => 'Join KhanVerse', 'description' => 'Sign up today and start your journey.', 'button_text' => 'Sign Up Now', 'button_url' => '/register', 'sort_order' => 4, 'status' => true],
        ];

        foreach ($sections as $section) {
            HomepageSection::firstOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}
