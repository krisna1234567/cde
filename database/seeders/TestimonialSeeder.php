<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Emily Jeff', 'role' => 'CEO, TheWebAgency', 'quote' => 'The team delivered a reliable solar solution with clear communication from planning through commissioning.', 'initials' => 'EJ', 'rating' => 5],
            ['name' => 'Hamza Malik', 'role' => 'Manager, TheWebTech', 'quote' => 'Our operational cost is more predictable and the installation process was handled professionally.', 'initials' => 'HM', 'rating' => 5],
            ['name' => 'Elizabeth Rai', 'role' => 'Developer, Leo Company', 'quote' => 'We value the technical detail, responsive support, and measurable energy performance.', 'initials' => 'ER', 'rating' => 5],
            ['name' => 'Sara Thomas', 'role' => 'Accountant, TheConstruction', 'quote' => 'The project was completed safely, on schedule, and with transparent reporting.', 'initials' => 'ST', 'rating' => 5],
            ['name' => 'David Lim', 'role' => 'Operations Director', 'quote' => 'A dependable partner for industrial-scale renewable energy implementation.', 'initials' => 'DL', 'rating' => 5],
            ['name' => 'Nadia Putri', 'role' => 'Sustainability Lead', 'quote' => 'CDE helped us translate our sustainability targets into a practical energy roadmap.', 'initials' => 'NP', 'rating' => 5],
        ];

        foreach ($testimonials as $index => $testimonial) {
            Testimonial::query()->updateOrCreate(
                ['name' => $testimonial['name'], 'role' => $testimonial['role']],
                array_merge($testimonial, [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }
}
