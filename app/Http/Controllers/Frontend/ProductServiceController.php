<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Service;
use Illuminate\Contracts\View\View;

class ProductServiceController extends PublicController
{
    public function index(): View
    {
        $page = $this->content->page('product-services');
        $sections = $this->content->sections($page);
        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $productsSection = $this->content->sectionData($this->content->section($sections, 'products'));
        $servicesSection = $this->content->sectionData($this->content->section($sections, 'services'));

        $products = Service::query()
            ->products()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Service $product) => $this->content->product($product))
            ->all();

        $services = Service::query()
            ->services()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Service $service) => $this->content->service($service))
            ->all();

        return $this->renderPage('public.products.index', $page, [
            'hero' => [
                'title' => $heroSection['title'] ?: $page->title,
                'description' => $heroSection['subtitle'] ?: $page->excerpt,
                'image' => $heroSection['image'] ?: 'images/pages/product-hero.webp',
                'image_alt' => $heroSection['image_alt'] ?: 'Solar panels under a cloudy sky',
            ],
            'productsSection' => $productsSection,
            'products' => $products,
            'servicesSection' => $servicesSection,
            'services' => $services,
        ], [
            'image' => $this->content->assetUrl($heroSection['image'], 'images/pages/product-hero.webp'),
            'canonical' => route('products.index'),
        ]);
    }

    public function show(string $slug): View
    {
        $page = $this->content->page('product-services');
        $model = Service::query()->products()->active()->where('slug', $slug)->firstOrFail();
        $product = $this->content->product($model);

        return $this->renderPage('public.products.show', $page, compact('product'), [
            'title' => $model->meta_title ?: $model->name.' - '.$this->content->site()['short_name'],
            'description' => $model->meta_description ?: $model->short_description,
            'og_title' => $model->meta_title ?: $model->name,
            'og_description' => $model->meta_description ?: $model->short_description,
            'image' => $this->content->assetUrl($model->og_image_path ?: $model->image_path, 'images/products/sun2000-2-3-4-5ktl.svg'),
            'canonical' => $model->canonical_url ?: route('products.show', $model->slug),
            'type' => 'product',
        ]);
    }
}
