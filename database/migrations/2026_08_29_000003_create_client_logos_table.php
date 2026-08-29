<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_logos', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('logo_alt')->nullable();
            $table->text('website_url')->nullable();
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
        Schema::dropIfExists('client_logos');
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
            ->where('page_sections.section_key', 'clients')
            ->orderBy('page_section_items.sort_order')
            ->select('page_section_items.*')
            ->get();

        foreach ($items as $item) {
            if (blank($item->title) || blank($item->image_path)) {
                continue;
            }

            DB::table('client_logos')->insert([
                'name' => $item->title,
                'logo_path' => $item->image_path,
                'logo_alt' => $item->image_alt ?: $item->title.' logo',
                'website_url' => $item->link_url,
                'sort_order' => $item->sort_order,
                'is_active' => $item->is_active,
                'created_at' => $item->created_at ?: now(),
                'updated_at' => $item->updated_at ?: now(),
            ]);
        }
    }
};
