<?php

namespace App\Models;

use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory, LogsContentActivity;

    public const MAIN_KEY = 'main';

    protected $fillable = [
        'key', 'company_name', 'short_name', 'tagline', 'logo_path', 'logo_alt', 'favicon_path', 'email',
        'contact_recipient_email', 'phone', 'whatsapp_number', 'whatsapp_default_message', 'address',
        'google_maps_embed_url', 'google_maps_link', 'social_links', 'footer_text', 'default_meta_title',
        'default_meta_description', 'default_og_image_path', 'impact_metrics',
    ];

    protected $casts = [
        'social_links' => 'array',
        'impact_metrics' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['key' => self::MAIN_KEY], ['company_name' => config('app.name')]);
    }

    public static function defaultImpactMetrics(): array
    {
        return [
            ['value' => '50+ MW', 'label' => 'Installed Capacity', 'sort_order' => 1, 'is_active' => true],
            ['value' => '120+', 'label' => 'Projects Completed', 'sort_order' => 2, 'is_active' => true],
            ['value' => '30+', 'label' => 'Clients Served', 'sort_order' => 3, 'is_active' => true],
            ['value' => '10+ Years', 'label' => 'Industry Experience', 'sort_order' => 4, 'is_active' => true],
        ];
    }

    public static function normalizeImpactMetrics(?array $metrics): array
    {
        return collect($metrics ?? [])
            ->filter(fn ($metric): bool => is_array($metric))
            ->map(function (array $metric, int $index): array {
                return [
                    'value' => trim((string) ($metric['value'] ?? '')),
                    'label' => trim((string) ($metric['label'] ?? '')),
                    'sort_order' => (int) ($metric['sort_order'] ?? ($index + 1)),
                    'is_active' => filter_var($metric['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];
            })
            ->filter(fn (array $metric): bool => $metric['value'] !== '' && $metric['label'] !== '')
            ->sortBy('sort_order')
            ->values()
            ->all();
    }

    public function impactMetrics(): array
    {
        return self::normalizeImpactMetrics($this->impact_metrics ?? self::defaultImpactMetrics());
    }
}
