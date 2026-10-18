<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductServiceCardsSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->where('page_key', 'product-services')->firstOrFail();

        DB::transaction(function () use ($page): void {
            $section = $page->sections()->firstOrCreate(['section_key' => 'solutions'], [
                'section_type' => 'card_grid',
                'title' => 'Solar Products & Services',
                'subtitle' => 'Explore our solar energy products and professional services to find the right solutions for your business and project needs.',
                'sort_order' => ($page->sections()->max('sort_order') ?? 0) + 1,
                'is_active' => true,
            ]);

            // Only initialize new sections; preserve all subsequent admin edits and deletions.
            if (! $section->wasRecentlyCreated) {
                return;
            }

            $section->items()->createMany([
                [
                    'title' => 'Solar Component',
                    'description' => 'Discover solar inverters and other high-quality components for your solar energy system.',
                    'image_path' => 'images/products/sun2000-2-3-4-5ktl.svg',
                    'image_alt' => 'Solar energy components',
                    'link_text' => 'Explore Products', 'link_url' => '/product-services/products',
                    'sort_order' => 1, 'is_active' => true,
                ],
                [
                    'title' => 'Solar Services',
                    'description' => 'Explore our solar energy services and find the right solution for your project.',
                    'image_path' => 'images/pages/service-engineer.webp',
                    'image_alt' => 'Solar services engineer',
                    'link_text' => 'Explore Services', 'link_url' => '/product-services/services',
                    'sort_order' => 2, 'is_active' => true,
                ],
            ]);
        });
    }
}
