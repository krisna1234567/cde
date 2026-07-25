<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 50)->default('main')->unique();
            $table->string('company_name');
            $table->string('short_name', 100)->nullable();
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_alt')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_recipient_email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('whatsapp_number', 30)->nullable();
            $table->text('whatsapp_default_message')->nullable();
            $table->text('address')->nullable();
            $table->text('google_maps_embed_url')->nullable();
            $table->text('google_maps_link')->nullable();
            $table->json('social_links')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('default_meta_title')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->string('default_og_image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
