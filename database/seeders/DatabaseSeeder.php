<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\EnsuresDemoSeedingIsAllowed;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use EnsuresDemoSeedingIsAllowed;

    public function run(): void
    {
        $this->ensureDemoSeedingIsAllowed();

        $this->call([
            DemoUserSeeder::class,
            DemoListingSeeder::class,
            DemoOfferSeeder::class,
            DemoNotificationSeeder::class,
        ]);
    }
}
