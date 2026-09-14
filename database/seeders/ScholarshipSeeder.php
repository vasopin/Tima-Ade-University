<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scholarship;

class ScholarshipSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'title' => 'Academic Merit Scholarship',
                'description' => 'Partial tuition award for top academic performers based on entrance assessment and past academic record.',
                'amount' => 2000.00,
                'criteria' => 'Top 5% of applicants by academic score',
                'is_active' => true,
            ],
            [
                'title' => 'Need-Based Support Grant',
                'description' => 'Assistance for families with demonstrated financial need, covering tuition and living costs in part.',
                'amount' => 1500.00,
                'criteria' => 'Means-tested application with documentation',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Scholarship::updateOrCreate(['title' => $item['title']], $item);
        }
    }
}
