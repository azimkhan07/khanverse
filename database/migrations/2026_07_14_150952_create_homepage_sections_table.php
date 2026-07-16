<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->enum('section_key', [
                'hero',
                'why_choose_us',
                'featured_categories',
                'featured_services',
                'featured_sellers',
                'latest_projects',
                'testimonials',
                'faq',
                'cta',
                'newsletter'
            ]);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('background_image')->nullable();
            $table->json('extra_data')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homepage_sections');
    }
};
