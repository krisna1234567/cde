<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $summary = [
            'products' => Service::query()->products()->count(),
            'services' => Service::query()->services()->count(),
            'portfolios' => Portfolio::query()->count(),
            'posts' => Post::query()->count(),
            'new_messages' => ContactMessage::query()->new()->count(),
        ];

        $activities = ActivityLog::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('summary', 'activities'));
    }
}
