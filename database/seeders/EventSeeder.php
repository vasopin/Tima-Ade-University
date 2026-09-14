<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'title' => 'Autumn Research Symposium',
                'slug' => 'autumn-research-symposium',
                'content' => 'Showcasing undergraduate and faculty research projects across STEM and humanities.',
                'location' => 'Auditorium A',
                'starts_at' => Carbon::now()->addDays(14),
                'ends_at' => Carbon::now()->addDays(14)->addHours(4),
                'is_active' => true,
            ],
            [
                'title' => 'Open Campus Visit Day',
                'slug' => 'campus-visit-day',
                'content' => 'Prospective students and families are invited to campus tours and information sessions.',
                'location' => 'Main Quad',
                'starts_at' => Carbon::now()->addDays(21),
                'ends_at' => Carbon::now()->addDays(21)->addHours(6),
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Event::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
