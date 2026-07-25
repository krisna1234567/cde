<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('page_key', 80)->unique();
            $table->string('name');
            $table->string('slug', 180)->unique();
            $table->string('title');
            $table->string('navigation_label')->nullable();
            $table->text('excerpt')->nullable();
            $table->boolean('show_in_navigation')->default(true);
            $table->unsignedSmallInteger('navigation_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->text('canonical_url')->nullable();
            $table->string('robots', 50)->default('index,follow');
            $table->timestamps();
            $table->index(['show_in_navigation', 'navigation_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
