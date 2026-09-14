<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ClassSeeder::class,
            SubjectSeeder::class,
            OfficialProgramCatalogSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            FeeSeeder::class,
            NoticeSeeder::class,
            \Database\Seeders\NotificationSeeder::class,
            \Database\Seeders\ParentSeeder::class,
            \Database\Seeders\TimetableSeeder::class,
            \Database\Seeders\FacilitySeeder::class,
            \Database\Seeders\EventSeeder::class,
            \Database\Seeders\TestimonialSeeder::class,
            \Database\Seeders\AccreditationSeeder::class,
            \Database\Seeders\ScholarshipSeeder::class,
        ]);

        if (app()->environment(['local', 'testing'])) {
            $this->call(DevelopmentDashboardAccountSeeder::class);
        }
    }
}
