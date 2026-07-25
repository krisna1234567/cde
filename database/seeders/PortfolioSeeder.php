<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['Heinz ABC Indonesia', 'PT Heinz ABC Indonesia', '405 kWp', 'Jl. Randupitu–Gunung Gangsir, Pasuruan, East Java', 'heinz-abc-indonesia.webp', 'heinz-abc.svg'],
            ['Bumimulia Indah Lestari', 'PT Bumimulia Indah Lestari', '752 kWp', 'Cikarang, West Java', 'bumimulia-indah-lestari.webp', 'bumienergi.svg'],
            ['Cleo Sariguna Prima', 'PT Sariguna Primatirta Tbk', '232 kWp', 'East Java, Indonesia', 'cleo-sariguna-prima.webp', 'cleo.svg'],
            ['JIIPE Industrial Estate', 'JIIPE Industrial Estate', '500 kWp', 'Gresik, East Java', 'jiipe-industrial-estate.webp', 'nusantara.svg'],
            ['Krakatau Pipe Industries', 'PT Krakatau Pipe Industries', '310 kWp', 'Cilegon, Banten', 'krakatau-pipe-industries.webp', 'polaris.svg'],
            ['Tansri Gani (TSG)', 'Tansri Gani', '417 kWp', 'Indonesia', 'tansri-gani.webp', 'kirana.svg'],
        ];

        foreach ($projects as $index => [$title, $client, $capacity, $location, $image, $logo]) {
            Portfolio::query()->updateOrCreate(['slug' => (string) str($title)->slug()], [
                'title' => $title,
                'client_name' => $client,
                'category' => 'Solar PV',
                'capacity' => $capacity,
                'location' => $location,
                'short_description' => 'Commercial and industrial solar energy project delivered by CDE.',
                'description' => 'The client implemented a solar energy system to support sustainability objectives, reduce operational emissions, and improve its long-term energy mix.',
                'overview' => 'Engineering, procurement, construction, interconnection, testing, commissioning, and performance monitoring for an on-grid solar PV system.',
                'cover_image_path' => 'images/projects/'.$image,
                'cover_image_alt' => $title.' solar installation',
                'main_image_path' => 'images/projects/'.$image,
                'main_image_alt' => $title.' main project image',
                'secondary_image_path' => 'images/site/company-overview.webp',
                'secondary_image_alt' => 'Solar panel installation detail',
                'client_logo_path' => 'images/clients/'.$logo,
                'client_logo_alt' => $client.' logo',
                'sort_order' => $index + 1,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => $title.' - Project CDE',
                'meta_description' => 'Learn about the '.$title.' solar project delivered by CDE.',
            ]);
        }
    }
}
