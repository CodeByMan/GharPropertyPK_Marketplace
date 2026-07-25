<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_listing_values_are_rejected_before_database_persistence(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->from(route('realtor.listing.create'))
            ->post(route('realtor.listing.store'), [
                'beds' => -1,
                'baths' => 25,
                'area' => 5,
                'city' => str_repeat('a', 101),
                'code' => str_repeat('b', 21),
                'street' => str_repeat('c', 151),
                'street_nr' => str_repeat('1', 21),
                'price' => 0,
            ])
            ->assertSessionHasErrors([
                'beds', 'baths', 'area', 'city', 'code', 'street', 'street_nr', 'price',
            ]);

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_valid_listing_values_are_stored_for_the_authenticated_owner(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('realtor.listing.store'), [
                'beds' => 3,
                'baths' => 2,
                'area' => 150,
                'city' => 'Karachi',
                'code' => '75500',
                'street' => 'Example Road',
                'street_nr' => '12-A',
                'price' => 25000000,
            ])
            ->assertRedirect(route('realtor.listing.index'));

        $this->assertDatabaseHas('listings', [
            'user_id' => $owner->id,
            'city' => 'Karachi',
            'price' => 25000000,
        ]);
    }

    public function test_cover_image_is_securely_stored_and_associated_when_property_is_created(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('realtor.listing.store'), [
                'beds' => 4,
                'baths' => 4,
                'area' => 280,
                'city' => 'Lahore',
                'code' => '54000',
                'street' => 'Gulberg Main Boulevard',
                'street_nr' => 'House 18',
                'price' => 42500000,
                'image' => UploadedFile::fake()->image('property-cover.jpg', 1200, 800)->size(750),
            ])
            ->assertRedirect(route('realtor.listing.index'));

        $listing = Listing::query()->firstOrFail();
        $image = $listing->images()->firstOrFail();

        Storage::disk('public')->assertExists($image->filename);
        $this->assertSame($listing->id, $image->listing_id);
    }

    public function test_unsafe_or_unsupported_cover_image_is_rejected(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('realtor.listing.store'), [
                'beds' => 2,
                'baths' => 2,
                'area' => 120,
                'city' => 'Islamabad',
                'code' => '44000',
                'street' => 'Blue Area',
                'street_nr' => 'Unit 7',
                'price' => 18000000,
                'image' => UploadedFile::fake()->create('property.svg', 25, 'image/svg+xml'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('listings', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }
}
