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
        'default_meta_description', 'default_og_image_path',
    ];

    protected $casts = ['social_links' => 'array'];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['key' => self::MAIN_KEY], ['company_name' => config('app.name')]);
    }
}
