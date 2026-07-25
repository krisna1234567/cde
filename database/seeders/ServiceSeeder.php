<?php

namespace Database\Seeders;

use App\Enums\ServiceType;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['SUN2000 2/3/4/5KTL', 'Huawei', 'sun2000-2-3-4-5ktl.svg', 40.99],
            ['SUN2000 3/4/5/6KTL-L1', 'Huawei', 'sun2000-3-4-5-6ktl-l1.svg', null],
            ['SUN2000 5/6/8/10KTL-M1', 'Huawei', 'sun2000-5-6-8-10ktl-m1.svg', null],
            ['SUN2000 12/15/17/20KTL-M2', 'Huawei', 'sun2000-12-15-17-20ktl-m2.svg', null],
            ['SUN2000 30/36/40KTL-M3', 'Huawei', 'sun2000-30-36-40ktl-m3.svg', null],
            ['SUN2000 50KTL-M3', 'Huawei', 'sun2000-50ktl-m3.svg', null],
            ['SUN2000 100KTL-M2', 'Huawei', 'sun2000-100ktl-m2.svg', null],
            ['LUNA2000 Energy Storage System', 'Huawei', 'luna2000-ess.svg', null],
        ];

        foreach ($products as $index => [$name, $brand, $image, $price]) {
            Service::query()->updateOrCreate(['slug' => (string) str($name)->slug()], [
                'item_type' => ServiceType::Product->value,
                'name' => $name,
                'brand' => $brand,
                'price' => $price,
                'currency' => 'USD',
                'short_description' => 'Reliable solar inverter product for commercial and industrial energy systems.',
                'description' => '<p>This product is designed for efficient solar energy conversion, reliable operation, and integration with modern monitoring systems.</p><p>Final specifications and commercial terms should be confirmed with the CDE engineering team.</p>',
                'image_path' => 'images/products/'.$image,
                'image_alt' => $name,
                'sort_order' => $index + 1,
                'is_featured' => $index < 4,
                'is_active' => true,
                'meta_title' => $name.' - CDE',
                'meta_description' => 'Product information for '.$name.' by '.$brand.'.',
            ]);
        }

        $services = [
            ['Consultancy & Engineering', 'bi-people', 'Professional consultation and engineering for efficient and reliable energy systems.'],
            ['System Integration', 'bi-bezier2', 'Integration of solar systems with existing infrastructure for optimal performance.'],
            ['Procurement', 'bi-box-seam', 'Supply of high-quality components and equipment for reliable energy systems.'],
            ['Construction', 'bi-tools', 'Professional installation and construction services for solar energy projects.'],
            ['Testing & Commissioning', 'bi-diagram-3', 'Verification that every system operates safely and performs as designed.'],
            ['Maintenance & System Upgrade', 'bi-check-circle', 'Monitoring, maintenance, and upgrades for long-term performance.'],
        ];

        foreach ($services as $index => [$name, $icon, $description]) {
            Service::query()->updateOrCreate(['slug' => (string) str($name)->slug()], [
                'item_type' => ServiceType::Service->value,
                'name' => $name,
                'short_description' => $description,
                'description' => '<p>'.$description.'</p>',
                'icon' => $icon,
                'sort_order' => $index + 1,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }
    }
}
