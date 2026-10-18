<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Service;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ProductServiceController extends PublicController
{
    public function index(): View
    {
        $page = $this->content->page('product-services');
        $sections = $this->content->sections($page);
        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $solutionsSection = $this->content->sectionData($this->content->section($sections, 'solutions'));

        return $this->renderPage('public.products.index', $page, [
            'hero' => [
                'title' => $heroSection['title'] ?: $page->title,
                'description' => $heroSection['subtitle'] ?: $page->excerpt,
                'image' => $heroSection['image'] ?: 'images/pages/product-hero.webp',
                'image_alt' => $heroSection['image_alt'] ?: 'Solar panels under a cloudy sky',
            ],
            'solutionsSection' => $solutionsSection,
        ], [
            'image' => $this->content->assetUrl($heroSection['image'], 'images/pages/product-hero.webp'),
            'canonical' => route('products.index'),
        ]);
    }

    public function catalog(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80', \Illuminate\Validation\Rule::exists('product_categories', 'name')],
        ]);
        $search = trim($filters['q'] ?? '');
        $category = $filters['category'] ?? '';
        $page = $this->content->page('product-services');
        $products = Service::query()->products()->active()
            ->when($category !== '', fn ($query) => $query->where('product_category', $category))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('brand', 'like', '%'.$search.'%')
                        ->orWhere('short_description', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('sort_order')->orderBy('name')->paginate(16)->withQueryString();
        $products->through(fn (Service $product) => $this->content->product($product));

        return $this->renderPage('public.products.catalog', $page, [
            'products' => $products,
            'categories' => ProductCategory::query()->orderBy('name')->get(),
            'search' => $search,
            'category' => $category,
        ], [
            'title' => 'Our Solar Products - '.$this->content->site()['short_name'],
            'description' => 'Discover reliable solar products for your solar energy system.',
            'canonical' => route('products.catalog'),
        ]);
    }

    public function services(): View
    {
        $page = $this->content->page('product-services');
        $sections = $this->content->sections($page);
        $servicesSection = $this->content->sectionData($this->content->section($sections, 'services'));

        $services = Service::query()
            ->services()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Service $service) => $this->content->service($service))
            ->all();

        return $this->renderPage('public.products.services', $page, [
            'servicesSection' => $servicesSection,
            'services' => $services,
        ], [
            'title' => 'Our Service - '.$this->content->site()['short_name'],
            'description' => $servicesSection['subtitle'] ?: 'Explore CDE services across consultation, engineering, procurement, construction, commissioning, and maintenance.',
            'image' => $this->content->assetUrl($servicesSection['image'], 'images/pages/service-engineer.webp'),
            'canonical' => route('products.services'),
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

    public function proposal(string $slug): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $service = Service::query()->services()->active()->where('slug', $slug)->firstOrFail();
        abort_unless($service->hasProposal(), 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->response($service->proposal_path, $service->slug.'.pdf', [
            'Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }

}
