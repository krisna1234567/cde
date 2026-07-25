<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug', 180)->unique();
            $table->string('client_name')->nullable();
            $table->string('category')->nullable()->index();
            $table->date('project_date')->nullable();
            $table->string('capacity', 100)->nullable();
            $table->text('location')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('overview')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('cover_image_alt')->nullable();
            $table->string('main_image_path')->nullable();
            $table->string('main_image_alt')->nullable();
            $table->string('secondary_image_path')->nullable();
            $table->string('secondary_image_alt')->nullable();
            $table->string('client_logo_path')->nullable();
            $table->string('client_logo_alt')->nullable();
            $table->text('project_url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->text('canonical_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'sort_order']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
