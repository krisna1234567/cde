<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ContactMessageStatus;
use App\Http\Requests\Frontend\StoreContactRequest;
use App\Models\ContactMessage;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends PublicController
{
    public function index(): View
    {
        $page = $this->content->page('contact');
        $sections = $this->content->sections($page);
        $heroSection = $this->content->sectionData($this->content->section($sections, 'hero'));
        $contactSection = $this->content->sectionData($this->content->section($sections, 'contact-info'));
        $mapSection = $this->content->sectionData($this->content->section($sections, 'google-map'));

        return $this->renderPage('public.contact', $page, [
            'hero' => [
                'title' => $heroSection['title'] ?: $page->title,
                'description' => $heroSection['subtitle'] ?: $page->excerpt,
                'image' => $heroSection['image'] ?: 'images/pages/contact-hero.webp',
                'image_alt' => $heroSection['image_alt'] ?: 'Solar engineer working beside photovoltaic panels',
            ],
            'contactSection' => $contactSection,
            'mapSection' => $mapSection,
        ], [
            'image' => $this->content->assetUrl($heroSection['image'], 'images/pages/contact-hero.webp'),
            'canonical' => route('contact.index'),
        ]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $this->saveMessage($request, ['source_url' => url()->previous()]);

        return back()->with('success', 'Thank you. Your message has been sent successfully.');
    }

    public function sales(string $slug): View
    {
        $product = Service::query()->products()->active()->where('slug', $slug)->firstOrFail();

        return $this->render('public.products.contact-sales', [
            'product' => $product,
            'defaultMessage' => 'saya mau tau lebih detail terkait produk '.$product->name.'.',
        ], [
            'title' => 'Contact Sales - '.$product->name,
            'description' => 'Connect with our sales team to learn more about '.$product->name.'.',
            'canonical' => route('products.sales', $product->slug),
        ]);
    }

    public function storeSales(StoreContactRequest $request, string $slug): RedirectResponse
    {
        $product = Service::query()->products()->active()->where('slug', $slug)->firstOrFail();
        $this->saveMessage($request, [
            'subject' => Str::limit('Product inquiry: '.$product->name, 255, ''),
            'source_url' => route('products.sales', $product->slug),
        ]);

        return redirect()->route('products.sales', $product->slug)
            ->with('success', 'Thank you. Your message has been sent successfully.');
    }

    private function saveMessage(StoreContactRequest $request, array $context): void
    {
        $data = $request->safe()->except('website');
        $data = array_merge($data, $context);
        $data['status'] = ContactMessageStatus::New->value;

        ContactMessage::query()->create($data);

    }
}
