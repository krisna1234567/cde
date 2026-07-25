<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PublicContentService;
use Illuminate\Contracts\View\View;

abstract class PublicController extends Controller
{
    public function __construct(protected PublicContentService $content)
    {
    }

    protected function render(string $view, array $data = [], array $meta = []): View
    {
        $setting = $this->content->siteSetting();
        $defaults = [
            'title' => $setting->default_meta_title ?: $setting->company_name,
            'description' => $setting->default_meta_description ?: 'Solar PV and renewable energy solutions for businesses across Indonesia.',
            'og_title' => $setting->default_meta_title ?: $setting->company_name,
            'og_description' => $setting->default_meta_description ?: 'Solar PV and renewable energy solutions for businesses across Indonesia.',
            'image' => $this->content->assetUrl($setting->default_og_image_path, 'images/site/hero.webp'),
            'canonical' => url()->current(),
            'robots' => 'index,follow',
            'type' => 'website',
        ];

        return view($view, array_merge(
            $this->content->shared(),
            $data,
            ['meta' => array_merge($defaults, array_filter($meta, fn ($value) => $value !== null))]
        ));
    }

    protected function renderPage(string $view, Page $page, array $data = [], array $metaOverrides = []): View
    {
        return $this->render($view, $data, $this->content->pageMeta($page, $metaOverrides));
    }
}
