<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('quote');
            $table->string('initials', 4)->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'sort_order']);
        });

        $this->copyExistingSectionItems();
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }

    private function copyExistingSectionItems(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_items')) {
            return;
        }

        $items = DB::table('page_section_items')
            ->join('page_sections', 'page_section_items.page_section_id', '=', 'page_sections.id')
            ->join('pages', 'page_sections.page_id', '=', 'pages.id')
            ->where('pages.page_key', 'home')
            ->where('page_sections.section_key', 'testimonials')
            ->orderBy('page_section_items.sort_order')
            ->select('page_section_items.*')
            ->get();

        foreach ($items as $item) {
            if (blank($item->title) || blank($item->description)) {
                continue;
            }

            $settings = json_decode((string) $item->settings, true);
            $settings = is_array($settings) ? $settings : [];
            $rating = max(1, min(5, (int) ($settings['rating'] ?? 5)));

            DB::table('testimonials')->insert([
                'name' => $item->title,
                'role' => $item->subtitle,
                'quote' => $item->description,
                'initials' => $settings['initials'] ?? null,
                'rating' => $rating,
                'sort_order' => $item->sort_order,
                'is_active' => $item->is_active,
                'created_at' => $item->created_at ?: now(),
                'updated_at' => $item->updated_at ?: now(),
            ]);
        }
    }
};
