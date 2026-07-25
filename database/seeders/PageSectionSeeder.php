<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'home' => [
                [
                    'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'Welcome to Green World',
                    'subtitle' => 'Reduce Operational Costs While Supporting Sustainable Energy',
                    'image_path' => 'images/site/hero.webp', 'image_alt' => 'Large-scale solar power plant viewed from above',
                    'button_text' => 'About Us', 'button_url' => '/about',
                    'settings' => ['secondary_button_text' => 'Work with us', 'secondary_button_url' => '/contact'],
                ],
                [
                    'section_key' => 'calculator', 'section_type' => 'calculator', 'title' => 'Calculate Your Solar Potential',
                    'image_path' => 'images/site/solar-calculator.webp', 'image_alt' => 'A customer reviewing a solar energy calculation',
                    'settings' => ['defaults' => [
                        'monthly_bill' => 10000000, 'installed_power' => 100, 'maximum_capacity' => 10,
                        'available_space' => 76, 'bill_savings' => 2000000, 'bill_with_solar' => 8000000,
                    ]],
                ],
                [
                    'section_key' => 'company-overview', 'section_type' => 'image_text', 'title' => 'Driving the Future of Renewable Energy',
                    'content' => '<p>specializes in the development and installation of Solar PV systems, delivering clean and sustainable energy solutions for various sectors across Indonesia.</p>',
                    'image_path' => 'images/site/company-overview.webp',
                    'image_alt' => 'Rows of rooftop solar panels installed on an industrial facility',
                ],
                ['section_key' => 'impact', 'section_type' => 'statistics', 'title' => 'Our Impact in Numbers'],
                ['section_key' => 'clients', 'section_type' => 'logo_grid', 'title' => 'Trusted by leading companies across various industries in Indonesia'],
                [
                    'section_key' => 'benefits', 'section_type' => 'feature_grid', 'title' => 'Why Solar Energy is the Best Choice',
                    'subtitle' => 'Solar panels deliver clean, sustainable energy while reducing electricity costs and environmental impact.',
                ],
                [
                    'section_key' => 'hybrid-system', 'section_type' => 'image_text', 'title' => 'How Hybrid Solar System Works',
                    'content' => '<p>Understand how our hybrid system efficiently manages energy from solar panels, battery storage, and the grid to power your building reliably.</p>',
                    'image_path' => 'images/site/hybrid-system.webp',
                    'image_alt' => 'Hybrid solar system diagram connecting a home, battery, grid, and photovoltaic panels',
                ],
                [
                    'section_key' => 'featured-projects', 'section_type' => 'portfolio_listing', 'title' => 'Projects That Power the Future',
                    'subtitle' => 'Take a look at some of our solar energy projects helping businesses transition to clean and sustainable power.',
                ],
                [
                    'section_key' => 'testimonials', 'section_type' => 'testimonials', 'title' => 'What Do They Say About CDE?',
                    'subtitle' => 'Businesses trust us to manage their transition to reliable and sustainable energy.',
                ],
                [
                    'section_key' => 'contact-cta', 'section_type' => 'cta', 'title' => 'Get in Touch',
                    'subtitle' => 'Contact our team to discuss how solar energy can support your business.',
                    'image_path' => 'images/pages/contact-cta.webp', 'image_alt' => 'Solar engineers at a photovoltaic installation',
                    'button_text' => 'Contact Us', 'button_url' => '/contact',
                ],
            ],
            'about' => [
                [
                    'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'About Us',
                    'subtitle' => 'Reduce Operational Costs While Supporting Sustainable Energy',
                    'image_path' => 'images/pages/about-hero.webp', 'image_alt' => 'Solar panel installation',
                ],
                [
                    'section_key' => 'history', 'section_type' => 'rich_text', 'title' => 'History',
                    'content' => '<p>Based in Bandung, Indonesia, PT Cipta Daya Engineering is a team of experienced professionals, engineers, and technicians dedicated to delivering reliable and innovative energy solutions. We work collaboratively to provide the best approaches in New Energy Solutions and Next Generation Control systems for various industries.</p><p>Our expertise covers a wide range of services, including consultancy, engineering, procurement, and construction (EPC). With extensive project experience across Indonesia, we are capable of delivering advanced solutions in control systems, instrumentation, and electric power systems. Through our commitment to quality, innovation, and sustainability, we aim to support the development of efficient and reliable energy infrastructure for businesses and communities.</p>',
                ],
                ['section_key' => 'vision-mission', 'section_type' => 'feature_grid', 'title' => 'Our Vision and Mission for the Future'],
                ['section_key' => 'why-choose', 'section_type' => 'feature_grid', 'title' => 'Why Choose CDE?'],
                ['section_key' => 'team', 'section_type' => 'team', 'title' => 'Meet Our Teams'],
            ],
            'product-services' => [
                [
                    'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'Product & Service',
                    'subtitle' => 'Comprehensive solar energy solutions designed to support efficient and sustainable power systems.',
                    'image_path' => 'images/pages/product-hero.webp', 'image_alt' => 'Solar panels under a cloudy sky',
                ],
                [
                    'section_key' => 'products', 'section_type' => 'product_listing', 'title' => 'Our Solar Inverter Products',
                    'subtitle' => 'Discover reliable and high-efficiency inverter solutions designed to maximize the performance of your solar energy system.',
                ],
                [
                    'section_key' => 'services', 'section_type' => 'service_listing', 'title' => 'Our Services',
                    'subtitle' => 'From consultation to system maintenance, we deliver end-to-end solar and engineering services for efficient energy systems.',
                    'image_path' => 'images/pages/service-engineer.webp', 'image_alt' => 'Solar energy engineer inspecting panels',
                ],
            ],
            'projects' => [
                [
                    'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'Project',
                    'subtitle' => 'Take a look at some of our solar energy projects helping businesses transition to clean and sustainable power.',
                    'image_path' => 'images/pages/project-hero.webp', 'image_alt' => 'Solar engineers reviewing a photovoltaic installation',
                ],
                ['section_key' => 'projects', 'section_type' => 'portfolio_listing', 'title' => 'Projects That Power the Future'],
            ],
            'media' => [
                [
                    'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'Media & Updates',
                    'subtitle' => 'Stay updated with our latest news, project highlights, and company activities in advancing renewable energy solutions.',
                    'image_path' => 'images/pages/media-hero.webp', 'image_alt' => 'Renewable energy media illustration',
                ],
                [
                    'section_key' => 'latest-news', 'section_type' => 'post_listing', 'title' => 'Latest News',
                    'subtitle' => 'Explore the latest announcements, partnerships, and milestones from Cipta Daya Engineering.',
                ],
            ],
            'contact' => [
                [
                    'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'Contact Us',
                    'subtitle' => 'Get in touch with our team to learn more about our solar energy solutions and engineering services.',
                    'image_path' => 'images/pages/contact-hero.webp', 'image_alt' => 'Solar engineer working beside photovoltaic panels',
                ],
                [
                    'section_key' => 'contact-info', 'section_type' => 'contact', 'title' => 'Get in touch',
                    'subtitle' => 'Reach out to our team for inquiries, project discussions, or more information about our energy solutions.',
                ],
                ['section_key' => 'google-map', 'section_type' => 'map', 'title' => 'Visit Our Office'],
            ],
        ];

        foreach ($sections as $pageKey => $pageSections) {
            $page = Page::query()->where('page_key', $pageKey)->firstOrFail();

            foreach ($pageSections as $index => $sectionData) {
                PageSection::query()->updateOrCreate(
                    ['page_id' => $page->id, 'section_key' => $sectionData['section_key']],
                    array_merge([
                        'section_type' => 'rich_text', 'title' => null, 'subtitle' => null, 'content' => null,
                        'image_path' => null, 'image_alt' => null, 'button_text' => null, 'button_url' => null,
                        'settings' => null, 'sort_order' => $index + 1, 'is_active' => true,
                    ], $sectionData, ['sort_order' => $index + 1, 'is_active' => true])
                );
            }
        }

        $this->seedItems();
    }

    private function seedItems(): void
    {
        $items = [
            'home.impact' => [
                ['title' => '50+ MW', 'subtitle' => 'Installed Capacity'],
                ['title' => '120+', 'subtitle' => 'Projects Completed'],
                ['title' => '30+', 'subtitle' => 'Clients Served'],
                ['title' => '10+ Years', 'subtitle' => 'Industry Experience'],
            ],
            'home.benefits' => [
                ['title' => 'Eco-Friendly Energy', 'description' => 'Generate clean, renewable energy while reducing carbon emissions and environmental impact.', 'icon' => 'bi-leaf-fill'],
                ['title' => 'Reliable Power Backup', 'description' => 'Ensure a more reliable power supply when grid electricity is interrupted.', 'icon' => 'bi-battery-charging'],
                ['title' => 'Built for All Climates', 'description' => 'Designed to perform efficiently in various weather conditions, including tropical climates.', 'icon' => 'bi-brightness-high-fill'],
                ['title' => 'Versatile Solar Technology', 'description' => 'Solar technology supports multiple energy solutions beyond electricity generation.', 'icon' => 'bi-grid-3x3-gap-fill'],
                ['title' => 'Low Maintenance', 'description' => 'Solar panels require minimal maintenance and offer long-term performance.', 'icon' => 'bi-tools'],
                ['title' => 'Lower Electricity Costs', 'description' => 'Reduce electricity expenses by generating your own clean energy.', 'icon' => 'bi-lightning-charge-fill'],
            ],
            'home.clients' => [
                ['title' => 'AstraNova', 'image_path' => 'images/clients/astranova.svg'],
                ['title' => 'SuryaTech', 'image_path' => 'images/clients/suryatech.svg'],
                ['title' => 'Polaris', 'image_path' => 'images/clients/polaris.svg'],
                ['title' => 'BumiEnergi', 'image_path' => 'images/clients/bumienergi.svg'],
                ['title' => 'Kirana', 'image_path' => 'images/clients/kirana.svg'],
                ['title' => 'Lumen', 'image_path' => 'images/clients/lumen.svg'],
                ['title' => 'Nusantara', 'image_path' => 'images/clients/nusantara.svg'],
                ['title' => 'Cleo', 'image_path' => 'images/clients/cleo.svg'],
            ],
            'home.hybrid-system' => [
                ['title' => 'Solar Panel (PV)', 'description' => 'Generates clean energy from sunlight during the day.', 'icon' => 'bi-sun'],
                ['title' => 'Battery Storage', 'description' => 'Stores excess energy for use at night or during power outages.', 'icon' => 'bi-battery-half'],
                ['title' => 'Grid Connection', 'description' => 'Supplies additional power when needed and supports energy stability.', 'icon' => 'bi-diagram-3'],
                ['title' => 'Home / Building', 'description' => 'Uses energy from solar, battery, or grid seamlessly.', 'icon' => 'bi-house-door'],
            ],
            'home.testimonials' => [
                ['title' => 'Emily Jeff', 'subtitle' => 'CEO, TheWebAgency', 'description' => 'The team delivered a reliable solar solution with clear communication from planning through commissioning.', 'settings' => ['rating' => 5, 'initials' => 'EJ']],
                ['title' => 'Hamza Malik', 'subtitle' => 'Manager, TheWebTech', 'description' => 'Our operational cost is more predictable and the installation process was handled professionally.', 'settings' => ['rating' => 5, 'initials' => 'HM']],
                ['title' => 'Elizabeth Rai', 'subtitle' => 'Developer, Leo Company', 'description' => 'We value the technical detail, responsive support, and measurable energy performance.', 'settings' => ['rating' => 5, 'initials' => 'ER']],
                ['title' => 'Sara Thomas', 'subtitle' => 'Accountant, TheConstruction', 'description' => 'The project was completed safely, on schedule, and with transparent reporting.', 'settings' => ['rating' => 5, 'initials' => 'ST']],
                ['title' => 'David Lim', 'subtitle' => 'Operations Director', 'description' => 'A dependable partner for industrial-scale renewable energy implementation.', 'settings' => ['rating' => 5, 'initials' => 'DL']],
                ['title' => 'Nadia Putri', 'subtitle' => 'Sustainability Lead', 'description' => 'CDE helped us translate our sustainability targets into a practical energy roadmap.', 'settings' => ['rating' => 5, 'initials' => 'NP']],
            ],
            'about.vision-mission' => [
                ['title' => 'Vision', 'description' => 'Become a key player in accelerating sustainable growth and development.'],
                ['title' => 'Mission', 'description' => 'Energizing the transformation of society and industry to achieve a more productive, sustainable future.'],
            ],
            'about.why-choose' => [
                ['title' => 'Experienced Engineering Team', 'subtitle' => '01', 'description' => 'A multidisciplinary team with practical experience across engineering, construction, and energy operations.'],
                ['title' => 'Reliable Energy Solutions', 'subtitle' => '02', 'description' => 'Solutions are designed around each client’s operational requirements, site conditions, and performance targets.'],
                ['title' => 'High Quality Standards', 'subtitle' => '03', 'description' => 'A disciplined approach to engineering, procurement, safety, testing, and project handover.'],
            ],
            'about.team' => [
                ['title' => 'Bonnie Green', 'subtitle' => 'Senior Front-end Developer', 'description' => 'Supports digital product development and user experience across internal and client-facing platforms.', 'image_path' => 'images/team/bonnie-green.svg', 'settings' => ['socials' => ['facebook' => '#', 'twitter' => '#', 'github' => '#']]],
                ['title' => 'Thomas Lean', 'subtitle' => 'Engineering Manager', 'description' => 'Coordinates multidisciplinary engineering activities and ensures solutions meet technical requirements.', 'image_path' => 'images/team/thomas-lean.svg', 'settings' => ['socials' => ['facebook' => '#', 'twitter' => '#', 'github' => '#']]],
                ['title' => 'Jese Leos', 'subtitle' => 'Project Engineer', 'description' => 'Works with project teams from site assessment and design through construction and commissioning.', 'image_path' => 'images/team/jese-leos.svg', 'settings' => ['socials' => ['facebook' => '#', 'twitter' => '#', 'github' => '#']]],
                ['title' => 'Leslie Livingston', 'subtitle' => 'Business Development', 'description' => 'Helps clients explore practical renewable energy solutions aligned with operational and sustainability goals.', 'image_path' => 'images/team/leslie-livingston.svg', 'settings' => ['socials' => ['facebook' => '#', 'twitter' => '#', 'github' => '#']]],
            ],
        ];

        foreach ($items as $target => $sectionItems) {
            [$pageKey, $sectionKey] = explode('.', $target, 2);
            $section = PageSection::query()
                ->whereHas('page', fn ($query) => $query->where('page_key', $pageKey))
                ->where('section_key', $sectionKey)
                ->firstOrFail();

            $section->items()->delete();

            foreach ($sectionItems as $index => $item) {
                PageSectionItem::query()->create(array_merge([
                    'page_section_id' => $section->id,
                    'title' => null, 'subtitle' => null, 'description' => null, 'icon' => null,
                    'image_path' => null, 'image_alt' => null, 'link_text' => null, 'link_url' => null,
                    'settings' => null, 'sort_order' => $index + 1, 'is_active' => true,
                ], $item, [
                    'page_section_id' => $section->id,
                    'image_alt' => $item['image_alt'] ?? (($item['image_path'] ?? null) ? ($item['title'] ?? null) : null),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]));
            }
        }
    }
}
