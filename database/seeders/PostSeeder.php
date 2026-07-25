<?php

namespace Database\Seeders;

use App\Enums\PublishStatus;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->admins()->first();
        $titles = [
            'New Construction Technology Awareness', 'Solar Rooftop Commissioning Milestone',
            'Building Safer Renewable Energy Projects', 'Industrial Solar Performance Review',
            'CDE Expands Engineering Collaboration', 'Energy Efficiency for Manufacturing Facilities',
            'How Solar Monitoring Improves Operations', 'Preparing a Facility for Rooftop Solar',
            'Understanding Commercial Solar Feasibility', 'CDE Completes Another Clean Energy Project',
            'The Role of Preventive Maintenance in Solar', 'Renewable Energy Trends for Indonesian Industry',
        ];

        foreach ($titles as $index => $title) {
            $date = Carbon::create(2025, 4, 12, 11, 0, 0, 'Asia/Jakarta')->addDays($index * 4)->addHours($index % 5);
            Post::query()->updateOrCreate(['slug' => (string) str($title)->slug()], [
                'user_id' => $author?->id,
                'title' => $title,
                'category' => 'News',
                'excerpt' => 'Explore project insights, engineering practices, and practical updates from the CDE renewable energy team.',
                'content' => '<p>Renewable energy development requires the right combination of engineering, operational planning, and collaboration.</p><p>Every project begins with a review of the facility, electrical profile, available space, operating requirements, and expected performance.</p><p>Through disciplined engineering, quality control, and performance monitoring, solar energy can provide long-term operational value.</p>',
                'cover_image_path' => sprintf('images/media/news-%02d.webp', ($index % 6) + 1),
                'cover_image_alt' => $title,
                'status' => PublishStatus::Published->value,
                'is_featured' => $index < 3,
                'published_at' => $date,
                'meta_title' => $title.' - CDE Media',
                'meta_description' => 'Read '.$title.' from PT Cipta Daya Engineering.',
            ]);
        }
    }
}
