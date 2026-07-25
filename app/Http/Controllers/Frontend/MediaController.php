<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post;
use Illuminate\Contracts\View\View;

class MediaController extends PublicController
{
    public function index(): View
    {
        $page = $this->content->page('media');
        $sections = $this->content->sections($page);
        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $newsSection = $this->content->sectionData($this->content->section($sections, 'latest-news'));

        $posts = Post::query()
            ->published()
            ->with('author')
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        $posts = $posts->through(fn (Post $post) => $this->content->post($post));

        return $this->renderPage('public.media.index', $page, [
            'hero' => [
                'title' => $heroSection['title'] ?: $page->title,
                'description' => $heroSection['subtitle'] ?: $page->excerpt,
                'image' => $heroSection['image'] ?: 'images/pages/media-hero.webp',
                'image_alt' => $heroSection['image_alt'] ?: 'Renewable energy media illustration',
            ],
            'newsSection' => $newsSection,
            'posts' => $posts,
        ], [
            'image' => $this->content->assetUrl($heroSection['image'], 'images/pages/media-hero.webp'),
            'canonical' => route('media.index'),
        ]);
    }

    public function show(string $slug): View
    {
        $page = $this->content->page('media');
        $model = Post::query()->published()->with('author')->where('slug', $slug)->firstOrFail();
        $post = $this->content->post($model);

        return $this->renderPage('public.media.show', $page, compact('post'), [
            'title' => $model->meta_title ?: $model->title.' - '.$this->content->site()['short_name'].' Media',
            'description' => $model->meta_description ?: $model->excerpt,
            'og_title' => $model->meta_title ?: $model->title,
            'og_description' => $model->meta_description ?: $model->excerpt,
            'image' => $this->content->assetUrl($model->og_image_path ?: $model->cover_image_path, 'images/media/news-detail.webp'),
            'canonical' => $model->canonical_url ?: route('media.show', $model->slug),
            'type' => 'article',
        ]);
    }
}
