<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('impact_metrics')->nullable()->after('default_og_image_path');
        });

        $this->populateImpactMetrics();
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('impact_metrics');
        });
    }

    private function populateImpactMetrics(): void
    {
        $setting = DB::table('site_settings')->where('key', SiteSetting::MAIN_KEY)->first()
            ?: DB::table('site_settings')->first();

        if ($setting === null) {
            return;
        }

        $metrics = $this->metricsFromSectionItems();

        DB::table('site_settings')
            ->where('id', $setting->id)
            ->update([
                'impact_metrics' => json_encode($metrics === [] ? SiteSetting::defaultImpactMetrics() : $metrics),
                'updated_at' => now(),
            ]);
    }

    private function metricsFromSectionItems(): array
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_items')) {
            return [];
        }

        return DB::table('page_section_items')
            ->join('page_sections', 'page_section_items.page_section_id', '=', 'page_sections.id')
            ->join('pages', 'page_sections.page_id', '=', 'pages.id')
            ->where('pages.page_key', 'home')
            ->where('page_sections.section_key', 'impact')
            ->orderBy('page_section_items.sort_order')
            ->select('page_section_items.*')
            ->get()
            ->map(function (object $item): ?array {
                $value = trim((string) $item->title);
                $label = trim((string) ($item->subtitle ?: $item->description));

                if ($value === '' || $label === '') {
                    return null;
                }

                return [
                    'value' => $value,
                    'label' => $label,
                    'sort_order' => (int) $item->sort_order,
                    'is_active' => (bool) $item->is_active,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
};
