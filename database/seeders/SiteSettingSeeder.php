<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(['key' => SiteSetting::MAIN_KEY], [
            'company_name' => 'PT Cipta Daya Engineering',
            'short_name' => 'CDE',
            'tagline' => 'Solar Energy Solution',
            'logo_path' => 'images/site/logo.png',
            'logo_alt' => 'PT Cipta Daya Engineering logo',
            'email' => 'umaritb@gmail.com',
            'contact_recipient_email' => 'umaritb@gmail.com',
            'phone' => '+62 811 2257 000',
            'whatsapp_number' => '628112257000',
            'whatsapp_default_message' => 'Hello CDE, I would like to discuss a solar energy solution.',
            'address' => 'Jl. Cibaduyut No.55, Bandung, West Java',
            'google_maps_embed_url' => 'https://www.google.com/maps?q=Jl.%20Cibaduyut%20No.55%2C%20Bandung%2C%20Indonesia&output=embed',
            'google_maps_link' => 'https://www.google.com/maps/search/?api=1&query=Jl.%20Cibaduyut%20No.55%2C%20Bandung%2C%20Indonesia',
            'social_links' => [
                'whatsapp' => 'https://wa.me/628112257000',
                'twitter' => '#',
                'telegram' => '#',
                'instagram' => '#',
            ],
            'footer_text' => 'PT Cipta Daya Engineering All Rights Reserved',
            'default_meta_title' => 'CDE - Renewable Energy Solutions',
            'default_meta_description' => 'PT Cipta Daya Engineering delivers solar PV and renewable energy solutions for businesses across Indonesia.',
            'default_og_image_path' => 'images/site/hero.webp',
        ]);
    }
}
