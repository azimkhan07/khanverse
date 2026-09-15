<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Top 10 Tips For Freelancers In 2026',
                'category' => 'Freelancing',
                'author' => 'Azim Khan',
                'excerpt' => 'Practical tips that help freelancers grow faster and win more projects.',
                'content' => '<p>Freelancing has evolved into one of the most flexible and rewarding ways to work. Standing out takes strategy, discipline and the right habits.</p><p>Start by building a focused portfolio. A single well-presented case study beats a dozen half-finished projects. Clients want proof you can solve their specific problem, tailored to the work you actually want to win.</p><p>Pricing is where most freelancers hesitate. Research comparable projects, set a floor you can commit to, and never undercut yourself just to win a first client.</p>',
            ],
            [
                'title' => 'How AI Is Changing Remote Work',
                'category' => 'Technology',
                'author' => 'Sarah Wilson',
                'excerpt' => 'Artificial Intelligence is transforming how freelancers and businesses collaborate.',
                'content' => '<p>Artificial Intelligence is no longer a distant promise — it is reshaping how freelancers and businesses collaborate every single day.</p><p>From automated drafting to code assistance, AI tools let a single specialist deliver work that once required a full team. The key is treating AI as a collaborator, not a replacement for judgement.</p><p>The freelancers thriving in this new era combine tool expertise with strong communication and reliability — the human qualities no algorithm can replicate.</p>',
            ],
            [
                'title' => 'Best Productivity Tools For Developers',
                'category' => 'Development',
                'author' => 'Michael James',
                'excerpt' => 'A powerful collection of tools every developer should use daily.',
                'content' => '<p>Every developer builds their workflow around the tools they trust. The right set dramatically boosts speed, code quality and long-term maintainability.</p><p>Automation is the biggest leverage. Continuous integration, deployment pipelines and scriptable environments free you up to focus on solving real problems instead of repetitive housekeeping.</p><p>Finally, keep your stack focused. A small, reliable toolbelt that you understand deeply almost always beats a sprawling setup.</p>',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                array_merge($post, [
                    'cover_image' => null,
                    'published_at' => now()->subDays(rand(1, 30)),
                    'status' => true,
                    'meta_title' => $post['title'],
                ])
            );
        }
    }
}