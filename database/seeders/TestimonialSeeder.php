<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'name' => 'Aisha Khan',
                'role' => 'Alumnus, Class of 2022',
                'quote' => 'Tima-Ade University gave me the tools and mentorship to pursue engineering at top universities.',
                'photo_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=60',
                'is_active' => true,
            ],
            [
                'name' => 'Daniel Lee',
                'role' => 'Parent',
                'quote' => 'Attentive faculty and a clear pathway for student development set Tima-Ade University apart.',
                'photo_url' => 'https://images.unsplash.com/photo-1545996124-3d3a9b6b9b82?auto=format&fit=crop&w=400&q=60',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Testimonial::updateOrCreate(['name' => $item['name'], 'role' => $item['role']], $item);
        }
    }
}
