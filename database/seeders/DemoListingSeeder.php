<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use Database\Seeders\Concerns\EnsuresDemoSeedingIsAllowed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoListingSeeder extends Seeder
{
    use EnsuresDemoSeedingIsAllowed;

    private const DEMO_IMAGE_COUNT = 9;

    public function run(): void
    {
        $this->ensureDemoSeedingIsAllowed();

        $disk = Storage::disk('public');
        $disk->deleteDirectory('demo/listings');
        $listingPosition = 0;

        foreach (DemoMarketplaceData::LISTINGS as $key => $data) {
            $owner = User::query()->where('email', $data['owner'])->firstOrFail();

            $listing = Listing::withTrashed()
                ->where('user_id', $owner->id)
                ->where('city', $data['city'])
                ->where('street', $data['street'])
                ->where('street_nr', $data['street_nr'])
                ->first() ?? new Listing;

            $listing->forceFill([
                'user_id' => $owner->id,
                'beds' => $data['beds'],
                'baths' => $data['baths'],
                'area' => $data['area'],
                'city' => $data['city'],
                'code' => $data['code'],
                'street' => $data['street'],
                'street_nr' => $data['street_nr'],
                'price' => $data['price'],
                'sold_at' => isset($data['sold_days_ago'])
                    ? now()->subDays($data['sold_days_ago'])
                    : null,
                'deleted_at' => null,
                'created_at' => now()->subDays($data['created_days_ago']),
                'updated_at' => now()->subDay(),
            ])->saveQuietly();

            $listing->images()->delete();

            $imageCount = max(1, count($data['templates'] ?? [1, 2]));

            foreach ($this->imageNumbersForListing($listingPosition, $imageCount) as $imageIndex => $imageNumber) {
                $target = sprintf('demo/listings/%s-%d.webp', $key, $imageIndex + 1);
                $disk->put($target, $this->demoPropertyImage($imageNumber));
                $listing->images()->create(['filename' => $target]);
            }

            if (isset($data['deleted_days_ago'])) {
                $listing->forceFill([
                    'deleted_at' => now()->subDays($data['deleted_days_ago']),
                ])->saveQuietly();
            }

            $listingPosition++;
        }
    }

    /**
     * Return a stable pseudo-random selection of packaged photos.
     *
     * The order is deliberately deterministic so repeated seeding remains
     * idempotent while all nine photographs are distributed across the demo
     * catalogue instead of every property receiving the same placeholder.
     *
     * @return array<int, int>
     */
    private function imageNumbersForListing(int $listingPosition, int $count): array
    {
        $imagePool = range(1, self::DEMO_IMAGE_COUNT);

        usort(
            $imagePool,
            fn (int $left, int $right): int => strcmp(
                hash('sha256', "gharproperty-demo-image-{$left}"),
                hash('sha256', "gharproperty-demo-image-{$right}"),
            ),
        );

        $safeCount = min(max($count, 1), self::DEMO_IMAGE_COUNT);
        $selectedImages = [];

        for ($offset = 0; $offset < $safeCount; $offset++) {
            $poolIndex = ($listingPosition + ($offset * 4)) % self::DEMO_IMAGE_COUNT;
            $selectedImages[] = $imagePool[$poolIndex];
        }

        return $selectedImages;
    }

    private function demoPropertyImage(int $imageNumber): string
    {
        $path = public_path(sprintf(
            'images/gharproperty/properties/property-%02d.webp',
            $imageNumber,
        ));

        if (! is_file($path)) {
            throw new RuntimeException("Missing demo property image: {$path}");
        }

        $image = file_get_contents($path);

        if ($image === false) {
            throw new RuntimeException("Unable to read demo property image: {$path}");
        }

        return $image;
    }
}
