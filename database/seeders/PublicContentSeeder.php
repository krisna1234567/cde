<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PublicContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSettingSeeder::class,
            PageSeeder::class,
            PageSectionSeeder::class,
            TestimonialSeeder::class,
            ClientLogoSeeder::class,
            TeamMemberSeeder::class,
            ServiceSeeder::class,
            ProductServiceCardsSeeder::class,
            PortfolioSeeder::class,
            PostSeeder::class,
        ]);
    }
}
