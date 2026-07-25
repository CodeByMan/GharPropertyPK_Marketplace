<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Offer;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoMarketplaceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_marketplace_seed_is_complete_and_login_ready(): void
    {
        Storage::fake('public');
        $this->enableDemoSeeding();

        $this->app->make(DatabaseSeeder::class)->run();

        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('listings', 18);
        $this->assertDatabaseCount('listing_images', 36);
        $this->assertDatabaseCount('offers', 11);
        $this->assertDatabaseCount('notifications', 16);

        $this->assertSame(
            ['Faisalabad', 'Islamabad', 'Karachi', 'Lahore', 'Multan', 'Peshawar', 'Quetta', 'Rawalpindi', 'Sialkot'],
            Listing::withTrashed()->distinct()->orderBy('city')->pluck('city')->all(),
        );

        $this->assertSame(2, Listing::query()->whereNotNull('sold_at')->count());
        $this->assertSame(1, Listing::onlyTrashed()->count());
        $this->assertSame(2, Offer::query()->whereNotNull('accepted_at')->count());
        $this->assertSame(3, Offer::query()->whereNotNull('rejected_at')->count());

        foreach ([
            'admin@example.com',
            'seller@gharproperty.test',
            'agent@gharproperty.test',
            'buyer@gharproperty.test',
            'investor@gharproperty.test',
        ] as $email) {
            $user = User::query()->where('email', $email)->firstOrFail();

            $this->assertTrue(Hash::check('PortfolioDemo!2026', $user->password));
            $this->assertNotNull($user->email_verified_at);
        }

        $this->assertTrue(User::query()->where('email', 'admin@example.com')->firstOrFail()->is_admin);
        $this->assertFalse(User::query()->where('email', 'seller@gharproperty.test')->firstOrFail()->is_admin);
        $this->assertDatabaseMissing('users', ['email' => 'seller@gharmarkaz.test']);

        foreach (ListingImage::query()->pluck('filename') as $filename) {
            $this->assertStringEndsWith('.webp', $filename);
            Storage::disk('public')->assertExists($filename);
        }

        $this->assertCount(9, array_unique($this->seededImageHashes()));
    }

    public function test_demo_marketplace_seed_is_idempotent(): void
    {
        Storage::fake('public');
        $this->enableDemoSeeding();

        $seeder = $this->app->make(DatabaseSeeder::class);
        $seeder->run();
        $firstImageHashes = $this->seededImageHashes();

        $seeder->run();
        $secondImageHashes = $this->seededImageHashes();

        $this->assertSame($firstImageHashes, $secondImageHashes);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('listings', 18);
        $this->assertDatabaseCount('listing_images', 36);
        $this->assertDatabaseCount('offers', 11);
        $this->assertDatabaseCount('notifications', 16);

        $this->assertSame(16, DB::table('notifications')->distinct()->count('id'));
    }

    /**
     * @return array<string, string>
     */
    private function seededImageHashes(): array
    {
        return ListingImage::query()
            ->orderBy('filename')
            ->pluck('filename')
            ->mapWithKeys(fn (string $filename): array => [
                $filename => hash('sha256', Storage::disk('public')->get($filename)),
            ])
            ->all();
    }

    private function enableDemoSeeding(): void
    {
        $this->app['env'] = 'testing';

        config([
            'app.allow_demo_seeding' => true,
            'app.demo_password' => 'PortfolioDemo!2026',
        ]);
    }
}
