<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accreditation;
use Carbon\Carbon;

class AccreditationSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'name' => 'Regional Accreditation Board',
                'issuer' => 'Regional Accreditor',
                'issued_at' => Carbon::now()->subYears(2)->toDateString(),
                'expires_at' => Carbon::now()->addYears(3)->toDateString(),
                'logo_url' => 'https://images.unsplash.com/photo-1520962916835-9fe3b0e0a7b5?auto=format&fit=crop&w=400&q=60',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Accreditation::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
