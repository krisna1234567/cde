<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Portfolio;
use Illuminate\Contracts\View\View;

class ProjectController extends PublicController
{
    public function index(): View
    {
        $page = $this->content->page('projects');
        $sections = $this->content->sections($page);
        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $projectsSection = $this->content->sectionData($this->content->section($sections, 'projects'));

        $projects = Portfolio::query()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Portfolio $portfolio) => $this->content->portfolio($portfolio))
            ->all();

        return $this->renderPage('public.projects.index', $page, [
            'hero' => [
                'title' => $heroSection['title'] ?: $page->title,
                'description' => $heroSection['subtitle'] ?: $page->excerpt,
                'image' => $heroSection['image'] ?: 'images/pages/project-hero.webp',
                'image_alt' => $heroSection['image_alt'] ?: 'Solar engineers reviewing a photovoltaic installation',
            ],
            'projectsSection' => $projectsSection,
            'projects' => $projects,
        ], [
            'image' => $this->content->assetUrl($heroSection['image'], 'images/pages/project-hero.webp'),
            'canonical' => route('projects.index'),
        ]);
    }

    public function show(string $slug): View
    {
        $page = $this->content->page('projects');
        $model = Portfolio::query()->active()->where('slug', $slug)->firstOrFail();
        $project = $this->content->portfolio($model);

        return $this->renderPage('public.projects.show', $page, compact('project'), [
            'title' => $model->meta_title ?: $model->title.' - Project '.$this->content->site()['short_name'],
            'description' => $model->meta_description ?: $model->short_description ?: $model->description,
            'og_title' => $model->meta_title ?: $model->title,
            'og_description' => $model->meta_description ?: $model->short_description ?: $model->description,
            'image' => $this->content->assetUrl($model->og_image_path ?: $model->main_image_path ?: $model->cover_image_path, 'images/projects/heinz-abc-indonesia.webp'),
            'canonical' => $model->canonical_url ?: route('projects.show', $model->slug),
        ]);
    }
}
