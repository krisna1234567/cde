<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicContentService
{
    private ?SiteSetting $siteSetting = null;

    private ?array $sharedData = null;

    public function shared(): array
    {
        if ($this->sharedData !== null) {
            return $this->sharedData;
        }

        $site = $this->site();

        return $this->sharedData = [
            'site' => $site,
            'navigation' => $this->navigation(),
            'socials' => $this->socials($site),
        ];
    }

    public function siteSetting(): SiteSetting
    {
        return $this->siteSetting ??= SiteSetting::current();
    }

    public function site(): array
    {
        $setting = $this->siteSetting();
        $whatsapp = preg_replace('/\D+/', '', (string) $setting->whatsapp_number) ?: '';
        $contactPage = Page::query()->active()->where('page_key', 'contact')->first();

        return [
            'name' => $setting->company_name,
            'short_name' => $setting->short_name ?: $setting->company_name,
            'tagline' => $setting->tagline,
            'logo' => $this->publicPath($setting->logo_path, 'images/site/logo.png'),
            'logo_alt' => $setting->logo_alt ?: $setting->company_name.' logo',
            'favicon' => $this->publicPath($setting->favicon_path),
            'email' => $setting->email,
            'phone' => $setting->phone,
            'whatsapp' => $whatsapp,
            'whatsapp_message' => $setting->whatsapp_default_message ?: 'Hello, I would like to discuss your services.',
            'address' => $setting->address,
            'maps_embed_url' => $setting->google_maps_embed_url,
            'maps_link' => $setting->google_maps_link,
            'footer_text' => $setting->footer_text ?: $setting->company_name.' All Rights Reserved',
            'copyright' => $setting->footer_text ?: $setting->company_name.' All Rights Reserved',
            'contact_recipient_email' => $setting->contact_recipient_email,
            'contact_label' => $contactPage?->navigation_label ?: 'Contact',
        ];
    }

    public function navigation(): array
    {
        $routeMap = [
            'home' => ['route' => 'home', 'patterns' => ['home']],
            'about' => ['route' => 'about', 'patterns' => ['about']],
            'product-services' => ['route' => 'products.index', 'patterns' => ['products.*']],
            'projects' => ['route' => 'projects.index', 'patterns' => ['projects.*']],
            'media' => ['route' => 'media.index', 'patterns' => ['media.*']],
        ];

        return Page::query()
            ->inNavigation()
            ->whereIn('page_key', array_keys($routeMap))
            ->get()
            ->map(function (Page $page) use ($routeMap): array {
                $route = $routeMap[$page->page_key];

                return [
                    'label' => $page->navigation_label ?: $page->name,
                    'route' => $route['route'],
                    'patterns' => $route['patterns'],
                ];
            })
            ->values()
            ->all();
    }

    public function socials(array $site): array
    {
        $setting = $this->siteSetting();
        $links = collect($setting->social_links ?? []);

        if ($site['whatsapp'] !== '' && blank($links->get('whatsapp'))) {
            $links->put('whatsapp', 'https://wa.me/'.$site['whatsapp']);
        }

        $definitions = [
            'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'bi-whatsapp'],
            'linkedin' => ['label' => 'LinkedIn', 'icon' => 'bi-linkedin'],
            'twitter' => ['label' => 'Twitter', 'icon' => 'bi-twitter-x'],
            'telegram' => ['label' => 'Telegram', 'icon' => 'bi-telegram'],
            'instagram' => ['label' => 'Instagram', 'icon' => 'bi-instagram'],
            'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook'],
            'youtube' => ['label' => 'YouTube', 'icon' => 'bi-youtube'],
        ];

        return collect($definitions)
            ->map(function (array $definition, string $key) use ($links): ?array {
                $url = $links->get($key);

                if (blank($url)) {
                    return null;
                }

                return array_merge($definition, ['url' => $url]);
            })
            ->filter()
            ->values()
            ->all();
    }

    public function page(string $pageKey): Page
    {
        return Page::query()
            ->active()
            ->where('page_key', $pageKey)
            ->with([
                'sections' => fn ($query) => $query
                    ->active()
                    ->ordered()
                    ->with(['items' => fn ($itemQuery) => $itemQuery->active()->ordered()]),
            ])
            ->firstOrFail();
    }

    /**
     * @return Collection<string, PageSection>
     */
    public function sections(Page $page): Collection
    {
        return $page->sections->keyBy('section_key');
    }

    public function section(Collection $sections, string $key): ?PageSection
    {
        $section = $sections->get($key);

        return $section instanceof PageSection ? $section : null;
    }

    public function sectionData(?PageSection $section, array $fallback = []): array
    {
        if ($section === null) {
            return array_merge([
                'enabled' => false,
                'title' => null,
                'subtitle' => null,
                'content' => null,
                'paragraphs' => [],
                'image' => null,
                'image_alt' => null,
                'button_text' => null,
                'button_url' => null,
                'settings' => [],
                'items' => [],
            ], $fallback);
        }

        return array_merge([
            'enabled' => true,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'content' => $section->content,
            'paragraphs' => $this->paragraphs($section->content),
            'image' => $this->publicPath($section->image_path),
            'image_alt' => $section->image_alt ?: $section->title,
            'button_text' => $section->button_text,
            'button_url' => $section->button_url,
            'settings' => $section->settings ?? [],
            'items' => $section->items->map(fn (PageSectionItem $item) => $this->sectionItem($item))->values()->all(),
        ], $fallback);
    }

    public function sectionItem(PageSectionItem $item): array
    {
        return [
            'title' => $item->title,
            'subtitle' => $item->subtitle,
            'description' => $item->description,
            'icon' => $item->icon,
            'image' => $this->publicPath($item->image_path),
            'image_alt' => $item->image_alt ?: $item->title,
            'link_text' => $item->link_text,
            'link_url' => $item->link_url,
            'settings' => $item->settings ?? [],
        ];
    }

    public function product(Service $product): array
    {
        return [
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand,
            'price' => $product->price !== null ? (float) $product->price : null,
            'currency' => $product->currency,
            'currency_symbol' => $this->currencySymbol($product->currency),
            'short_description' => $product->short_description,
            'description' => $this->paragraphs($product->description),
            'image' => $this->publicPath($product->image_path, 'images/products/sun2000-2-3-4-5ktl.svg'),
            'image_alt' => $product->image_alt ?: $product->name,
        ];
    }

    public function service(Service $service): array
    {
        return [
            'title' => $service->name,
            'slug' => $service->slug,
            'icon' => $service->icon ?: 'bi-check-circle',
            'description' => $service->short_description ?: $this->plainText($service->description),
        ];
    }

    public function portfolio(Portfolio $portfolio): array
    {
        return [
            'title' => $portfolio->title,
            'slug' => $portfolio->slug,
            'client_name' => $portfolio->client_name,
            'capacity' => $portfolio->capacity,
            'card_capacity' => $portfolio->capacity,
            'location' => $portfolio->location,
            'description' => $portfolio->description ?: $portfolio->short_description,
            'overview' => $portfolio->overview,
            'cover_image' => $this->publicPath($portfolio->cover_image_path, 'images/projects/heinz-abc-indonesia.webp'),
            'cover_image_alt' => $portfolio->cover_image_alt ?: $portfolio->title.' solar project',
            'main_image' => $this->publicPath($portfolio->main_image_path ?: $portfolio->cover_image_path, 'images/projects/heinz-abc-indonesia.webp'),
            'main_image_alt' => $portfolio->main_image_alt ?: $portfolio->title.' main project image',
            'secondary_image' => $this->publicPath($portfolio->secondary_image_path, 'images/site/company-overview.webp'),
            'secondary_image_alt' => $portfolio->secondary_image_alt ?: 'Solar panel installation detail',
            'client_logo' => $this->publicPath($portfolio->client_logo_path, 'images/site/logo.png'),
            'client_logo_alt' => $portfolio->client_logo_alt ?: ($portfolio->client_name ?: $portfolio->title).' logo',
        ];
    }

    public function post(Post $post): array
    {
        $publishedAt = $post->published_at?->timezone(config('app.timezone'));

        return [
            'title' => $post->title,
            'slug' => $post->slug,
            'category' => $post->category,
            'excerpt' => $post->excerpt,
            'author' => $post->author?->name ?: $this->site()['short_name'],
            'published_date' => $publishedAt?->format('d F Y') ?: '',
            'published_time' => $publishedAt?->format('H:i').' WIB',
            'cover_image' => $this->publicPath($post->cover_image_path, 'images/media/news-detail.webp'),
            'cover_image_alt' => $post->cover_image_alt ?: $post->title,
            'content' => $this->paragraphs($post->content),
        ];
    }

    public function pageMeta(Page $page, array $overrides = []): array
    {
        $setting = $this->siteSetting();
        $defaults = [
            'title' => $page->meta_title ?: $setting->default_meta_title ?: $page->title,
            'description' => $page->meta_description ?: $page->excerpt ?: $setting->default_meta_description,
            'og_title' => $page->og_title ?: $page->meta_title ?: $page->title,
            'og_description' => $page->og_description ?: $page->meta_description ?: $page->excerpt ?: $setting->default_meta_description,
            'image' => $this->assetUrl($page->og_image_path ?: $setting->default_og_image_path, 'images/site/hero.webp'),
            'canonical' => $page->canonical_url ?: url()->current(),
            'robots' => $page->robots ?: 'index,follow',
            'type' => 'website',
        ];

        return array_merge($defaults, array_filter($overrides, fn ($value) => $value !== null));
    }

    public function paragraphs(?string $content): array
    {
        if (blank($content)) {
            return [];
        }

        $content = str_replace(["\r\n", "\r"], "\n", $content);
        preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $content, $matches);
        $parts = $matches[1] ?? [];

        if ($parts === []) {
            $parts = preg_split('/\n{2,}/', $content) ?: [];
        }

        return collect($parts)
            ->map(fn (string $paragraph): string => trim(html_entity_decode(strip_tags($paragraph), ENT_QUOTES | ENT_HTML5, 'UTF-8')))
            ->filter()
            ->values()
            ->all();
    }

    public function plainText(?string $content): string
    {
        return trim(implode(' ', $this->paragraphs($content)));
    }

    public function publicPath(?string $path, ?string $fallback = null): ?string
    {
        $path = filled($path) ? trim((string) $path) : $fallback;

        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'storage/', 'images/'])) {
            return $path;
        }

        return 'storage/'.ltrim($path, '/');
    }

    public function assetUrl(?string $path, ?string $fallback = null): string
    {
        $publicPath = $this->publicPath($path, $fallback);

        return $publicPath ? asset($publicPath) : url('/');
    }

    private function currencySymbol(?string $currency): string
    {
        return match (strtoupper((string) $currency)) {
            'USD' => '$',
            'IDR' => 'Rp',
            'EUR' => '€',
            default => strtoupper((string) $currency).' ',
        };
    }
}
