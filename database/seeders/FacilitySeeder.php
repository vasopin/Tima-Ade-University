<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'title' => 'Central Library',
                'slug' => 'central-library',
                'description' => 'A multi-level library with print and digital resources, study rooms, and research support services.',
                'icon' => 'bi-bookshelf',
                'image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=60',
                'is_active' => true,
            ],
            [
                'title' => 'Science Laboratories',
                'slug' => 'science-labs',
                'description' => 'Fully equipped chemistry, biology, and physics labs with modern instrumentation.',
                'icon' => 'bi-radioactive',
                'image_url' => 'https://images.unsplash.com/photo-1581093588401-1c7b90f83b0f?auto=format&fit=crop&w=1600&q=60',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Facility::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
