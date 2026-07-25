<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'page_key' => 'home', 'name' => 'Home', 'slug' => 'home', 'title' => 'Welcome to Green World',
                'navigation_label' => 'Home', 'navigation_order' => 1,
                'excerpt' => 'Reduce operational costs while supporting sustainable energy.',
                'meta_title' => 'CDE - Renewable Energy Solutions',
                'meta_description' => 'PT Cipta Daya Engineering delivers solar PV and renewable energy solutions for businesses across Indonesia.',
                'og_image_path' => 'images/site/hero.webp',
            ],
            [
                'page_key' => 'about', 'name' => 'About', 'slug' => 'about', 'title' => 'About Us',
                'navigation_label' => 'About', 'navigation_order' => 2,
                'excerpt' => 'Learn about the history, vision, mission, strengths, and team of PT Cipta Daya Engineering.',
                'meta_title' => 'About Us - CDE',
                'meta_description' => 'Learn about PT Cipta Daya Engineering, our history, vision, mission, strengths, and team.',
                'og_image_path' => 'images/pages/about-hero.webp',
            ],
            [
                'page_key' => 'product-services', 'name' => 'Product & Service', 'slug' => 'product-services', 'title' => 'Product & Service',
                'navigation_label' => 'Product & Service', 'navigation_order' => 3,
                'excerpt' => 'Comprehensive solar products and engineering services for efficient and sustainable power systems.',
                'meta_title' => 'Product & Service - CDE',
                'meta_description' => 'Explore solar inverter products and end-to-end renewable energy services from CDE.',
                'og_image_path' => 'images/pages/product-hero.webp',
            ],
            [
                'page_key' => 'projects', 'name' => 'Project', 'slug' => 'projects', 'title' => 'Project',
                'navigation_label' => 'Project', 'navigation_order' => 4,
                'excerpt' => 'Solar energy projects helping businesses transition to clean and sustainable power.',
                'meta_title' => 'Projects - CDE',
                'meta_description' => 'Explore renewable energy and solar PV projects delivered by PT Cipta Daya Engineering.',
                'og_image_path' => 'images/pages/project-hero.webp',
            ],
            [
                'page_key' => 'media', 'name' => 'Media', 'slug' => 'media', 'title' => 'Media & Updates',
                'navigation_label' => 'Media', 'navigation_order' => 5,
                'excerpt' => 'Latest news, project highlights, and renewable energy updates from CDE.',
                'meta_title' => 'Media & Updates - CDE',
                'meta_description' => 'Read the latest news, project highlights, and renewable energy updates from CDE.',
                'og_image_path' => 'images/pages/media-hero.webp',
            ],
            [
                'page_key' => 'contact', 'name' => 'Contact', 'slug' => 'contact', 'title' => 'Contact Us',
                'navigation_label' => 'Contact', 'navigation_order' => 6,
                'excerpt' => 'Contact our team to discuss solar energy solutions and engineering services.',
                'meta_title' => 'Contact Us - CDE',
                'meta_description' => 'Contact PT Cipta Daya Engineering to discuss solar energy solutions and engineering services.',
                'og_image_path' => 'images/pages/contact-hero.webp',
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(['page_key' => $page['page_key']], array_merge($page, [
                'show_in_navigation' => true,
                'is_active' => true,
                'robots' => 'index,follow',
            ]));
        }
    }
}
