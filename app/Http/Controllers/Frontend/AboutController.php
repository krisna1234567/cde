<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Contracts\View\View;

class AboutController extends PublicController
{
    public function __invoke(): View
    {
        $page = $this->content->page('about');
        $sections = $this->content->sections($page);

        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $historySection = $this->content->sectionData($this->content->section($sections, 'history'));
        $visionMissionSection = $this->content->sectionData($this->content->section($sections, 'vision-mission'));
        $advantagesSection = $this->content->sectionData($this->content->section($sections, 'why-choose'));
        $teamSection = $this->content->sectionData($this->content->section($sections, 'team'));

        $vision = collect($visionMissionSection['items'])->first(fn (array $item) => strcasecmp((string) $item['title'], 'Vision') === 0);
        $mission = collect($visionMissionSection['items'])->first(fn (array $item) => strcasecmp((string) $item['title'], 'Mission') === 0);

        $teams = collect($teamSection['items'])->map(fn (array $item): array => [
            'name' => $item['title'],
            'position' => $item['subtitle'],
            'description' => $item['description'],
            'image' => $item['image'] ?: 'images/team/bonnie-green.svg',
            'image_alt' => $item['image_alt'] ?: 'Portrait of '.$item['title'],
            'socials' => data_get($item, 'settings.socials', []),
        ])->all();

        return $this->renderPage('public.about', $page, [
            'hero' => [
                'title' => $heroSection['title'] ?: $page->title,
                'description' => $heroSection['subtitle'] ?: $page->excerpt,
                'image' => $heroSection['image'] ?: 'images/pages/about-hero.webp',
                'image_alt' => $heroSection['image_alt'] ?: 'Solar panel installation',
            ],
            'history' => [
                'title' => $historySection['title'],
                'paragraphs' => $historySection['paragraphs'],
            ],
            'visionMission' => [
                'title' => $visionMissionSection['title'],
                'vision_title' => data_get($vision, 'title', 'Vision'),
                'vision' => data_get($vision, 'description'),
                'mission_title' => data_get($mission, 'title', 'Mission'),
                'mission' => data_get($mission, 'description'),
            ],
            'advantagesSection' => $advantagesSection,
            'advantages' => collect($advantagesSection['items'])->map(fn (array $item): array => [
                'number' => $item['subtitle'],
                'title' => $item['title'],
                'description' => $item['description'],
            ])->all(),
            'teamSection' => $teamSection,
            'teams' => $teams,
        ], [
            'image' => $this->content->assetUrl($heroSection['image'], 'images/pages/about-hero.webp'),
            'canonical' => route('about'),
        ]);
    }
}
