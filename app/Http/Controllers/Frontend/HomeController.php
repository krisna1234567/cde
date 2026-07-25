<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class HomeController extends PublicController
{
    public function __invoke(): View
    {
        $page = $this->content->page('home');
        $sections = $this->content->sections($page);
        $site = $this->content->site();

        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $calculatorSection = $this->content->sectionData($this->content->section($sections, 'calculator'));
        $companySection = $this->content->sectionData($this->content->section($sections, 'company-overview'));
        $impactSection = $this->content->sectionData($this->content->section($sections, 'impact'));
        $clientsSection = $this->content->sectionData($this->content->section($sections, 'clients'));
        $benefitsSection = $this->content->sectionData($this->content->section($sections, 'benefits'));
        $hybridSection = $this->content->sectionData($this->content->section($sections, 'hybrid-system'));
        $projectsSection = $this->content->sectionData($this->content->section($sections, 'featured-projects'));
        $testimonialsSection = $this->content->sectionData($this->content->section($sections, 'testimonials'));
        $contactCtaSection = $this->content->sectionData($this->content->section($sections, 'contact-cta'));

        $hero = [
            'title' => $heroSection['title'] ?: $page->title,
            'description' => $heroSection['subtitle'] ?: $page->excerpt,
            'image' => $heroSection['image'] ?: 'images/site/hero.webp',
            'image_alt' => $heroSection['image_alt'] ?: 'Large-scale solar power plant viewed from above',
            'primary_button' => [
                'label' => $heroSection['button_text'] ?: 'About Us',
                'url' => $heroSection['button_url'] ?: route('about'),
            ],
            'secondary_button' => [
                'label' => data_get($heroSection, 'settings.secondary_button_text', 'Work with us'),
                'url' => data_get($heroSection, 'settings.secondary_button_url', route('contact.index')),
            ],
        ];

        $calculatorDefaults = data_get($calculatorSection, 'settings.defaults', []);
        $calculator = [
            'enabled' => $calculatorSection['enabled'],
            'title' => $calculatorSection['title'] ?: 'Calculate Your Solar Potential',
            'image' => $calculatorSection['image'] ?: 'images/site/solar-calculator.webp',
            'image_alt' => $calculatorSection['image_alt'] ?: 'A customer reviewing a solar energy calculation',
            'defaults' => array_merge([
                'monthly_bill' => 10000000,
                'installed_power' => 100,
                'maximum_capacity' => 10,
                'available_space' => 76,
                'bill_savings' => 2000000,
                'bill_with_solar' => 8000000,
            ], is_array($calculatorDefaults) ? $calculatorDefaults : []),
        ];

        $company = [
            'enabled' => $companySection['enabled'],
            'title' => $companySection['title'] ?: 'Driving the Future of Renewable Energy',
            'name' => $site['name'],
            'description' => $this->content->plainText($companySection['content']),
            'image' => $companySection['image'] ?: 'images/site/company-overview.webp',
            'image_alt' => $companySection['image_alt'] ?: 'Rows of rooftop solar panels installed on an industrial facility',
        ];

        $impact = collect($impactSection['items'])->map(fn (array $item): array => [
            'value' => $item['title'],
            'label' => $item['subtitle'] ?: $item['description'],
        ])->all();

        $clients = collect($clientsSection['items'])->map(fn (array $item): array => [
            'name' => $item['title'],
            'logo' => $item['image'],
            'image_alt' => $item['image_alt'] ?: $item['title'].' logo',
        ])->all();

        $benefits = collect($benefitsSection['items'])->map(fn (array $item): array => [
            'icon' => $item['icon'] ?: 'bi-check-circle',
            'title' => $item['title'],
            'description' => $item['description'],
        ])->all();

        $hybrid = [
            'enabled' => $hybridSection['enabled'],
            'title' => $hybridSection['title'],
            'description' => $this->content->plainText($hybridSection['content']),
            'image' => $hybridSection['image'] ?: 'images/site/hybrid-system.webp',
            'image_alt' => $hybridSection['image_alt'] ?: 'Hybrid solar system diagram',
            'items' => collect($hybridSection['items'])->map(fn (array $item): array => [
                'icon' => $item['icon'] ?: 'bi-check-circle',
                'title' => $item['title'],
                'description' => $item['description'],
            ])->all(),
        ];

        $projects = Portfolio::query()
            ->featured()
            ->orderBy('sort_order')
            ->limit(6)
            ->get()
            ->map(fn (Portfolio $portfolio) => $this->content->portfolio($portfolio))
            ->all();

        $testimonials = collect($testimonialsSection['items'])->map(fn (array $item): array => [
            'name' => $item['title'],
            'role' => $item['subtitle'],
            'initials' => data_get($item, 'settings.initials', Str::of((string) $item['title'])->explode(' ')->map(fn ($word) => Str::substr($word, 0, 1))->take(2)->implode('')),
            'rating' => (int) data_get($item, 'settings.rating', 5),
            'quote' => $item['description'],
        ])->all();

        $contactCta = [
            'enabled' => $contactCtaSection['enabled'],
            'title' => $contactCtaSection['title'],
            'description' => $contactCtaSection['subtitle'] ?: $this->content->plainText($contactCtaSection['content']),
            'image' => $contactCtaSection['image'] ?: 'images/pages/contact-cta.webp',
            'button_text' => $contactCtaSection['button_text'] ?: 'Contact Us',
            'button_url' => $contactCtaSection['button_url'] ?: route('contact.index'),
        ];

        return $this->renderPage('public.home', $page, [
            'hero' => $hero,
            'calculator' => $calculator,
            'company' => $company,
            'impactSection' => $impactSection,
            'impact' => $impact,
            'clientsSection' => $clientsSection,
            'clients' => $clients,
            'benefitsSection' => $benefitsSection,
            'benefits' => $benefits,
            'hybrid' => $hybrid,
            'projectsSection' => $projectsSection,
            'projects' => $projects,
            'testimonialsSection' => $testimonialsSection,
            'testimonials' => $testimonials,
            'contactCta' => $contactCta,
        ], [
            'image' => $this->content->assetUrl($hero['image']),
            'canonical' => route('home'),
        ]);
    }
}
