<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
        ];

        $messages = ContactMessage::query()
            ->when($filters['search'], function ($query, string $search): void {
                $like = '%'.$search.'%';

                $query->where(function ($query) use ($like): void {
                    $query->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('subject', 'like', $like)
                        ->orWhere('message', 'like', $like);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.contact-messages.index', [
            'filters' => $filters,
            'messages' => $messages,
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        return view('admin.contact-messages.show', compact('contactMessage'));
    }
}
