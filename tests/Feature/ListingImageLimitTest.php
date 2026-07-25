<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ListingImageLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_image_upload_is_accepted(): void
    {
        Storage::fake('public');
        [$owner, $listing] = $this->ownedListing();

        $this->actingAs($owner)
            ->post(route('realtor.listing.image.store', $listing), [
                'images' => [UploadedFile::fake()->image('property.jpg', 1200, 800)->size(500)],
            ])
            ->assertRedirect();

        $this->assertSame(1, $listing->images()->count());
        $this->assertCount(1, Storage::disk('public')->allFiles('images'));
    }

    public function test_more_than_ten_images_in_one_request_is_rejected(): void
    {
        Storage::fake('public');
        [$owner, $listing] = $this->ownedListing();
        $images = [];

        foreach (range(1, 11) as $index) {
            $images[] = UploadedFile::fake()->image("property-{$index}.jpg", 100, 100);
        }

        $this->actingAs($owner)
            ->post(route('realtor.listing.image.store', $listing), ['images' => $images])
            ->assertSessionHasErrors('images');

        $this->assertSame(0, $listing->images()->count());
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_malformed_image_file_is_rejected(): void
    {
        Storage::fake('public');
        [$owner, $listing] = $this->ownedListing();

        $this->actingAs($owner)
            ->post(route('realtor.listing.image.store', $listing), [
                'images' => [UploadedFile::fake()->create('fake.jpg', 20, 'image/jpeg')],
            ])
            ->assertSessionHasErrors('images.0');

        $this->assertSame(0, $listing->images()->count());
    }

    public function test_stored_files_are_cleaned_up_when_database_persistence_fails(): void
    {
        Storage::fake('public');
        [$owner, $listing] = $this->ownedListing();
        $event = 'eloquent.creating: '.ListingImage::class;

        Event::listen($event, function (): void {
            throw new RuntimeException('Simulated image database failure.');
        });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($owner)
                ->post(route('realtor.listing.image.store', $listing), [
                    'images' => [UploadedFile::fake()->image('cleanup.jpg', 100, 100)],
                ]);
            $this->fail('The simulated database exception was not thrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated image database failure.', $exception->getMessage());
        } finally {
            Event::forget($event);
        }

        $this->assertSame(0, $listing->images()->count());
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_total_listing_image_cap_is_enforced_before_storage(): void
    {
        Storage::fake('public');
        [$owner, $listing] = $this->ownedListing();

        foreach (range(1, 20) as $index) {
            $listing->images()->create(['filename' => "images/existing-{$index}.jpg"]);
        }

        $this->actingAs($owner)
            ->post(route('realtor.listing.image.store', $listing), [
                'images' => [UploadedFile::fake()->image('one-too-many.jpg', 100, 100)],
            ])
            ->assertSessionHasErrors('images');

        $this->assertSame(20, $listing->images()->count());
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    private function ownedListing(): array
    {
        $owner = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        return [$owner, $listing];
    }
}
