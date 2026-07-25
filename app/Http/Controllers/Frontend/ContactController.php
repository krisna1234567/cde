<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ContactMessageStatus;
use App\Http\Requests\Frontend\StoreContactRequest;
use App\Models\ContactMessage;
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
        $data = $request->safe()->except('website');
        $data['source_url'] = url()->previous();
        $data['status'] = ContactMessageStatus::New->value;

        ContactMessage::query()->create($data);

        return back()->with('success', 'Thank you. Your message has been sent successfully.');
    }
}
