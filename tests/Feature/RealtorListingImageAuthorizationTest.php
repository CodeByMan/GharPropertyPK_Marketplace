<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealtorListingImageAuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_owner_can_delete_an_image_from_their_listing(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['is_admin' => false]);
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $image = $listing->images()->create(['filename' => 'images/owner-image.jpg']);
        Storage::disk('public')->put($image->filename, 'image contents');

        $response = $this
            ->actingAs($owner)
            ->from(route('realtor.listing.image.create', $listing))
            ->delete(route('realtor.listing.image.destroy', [$listing, $image]));

        $response->assertRedirect(route('realtor.listing.image.create', $listing));
        $this->assertDatabaseMissing('listing_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($image->filename);
    }

    public function test_non_owner_cannot_manage_another_users_listing_images(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create(['is_admin' => false]);
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $image = $listing->images()->create(['filename' => 'images/protected.jpg']);
        Storage::disk('public')->put($image->filename, 'image contents');

        $this
            ->actingAs($otherUser)
            ->get(route('realtor.listing.image.create', $listing))
            ->assertForbidden();

        $this
            ->actingAs($otherUser)
            ->delete(route('realtor.listing.image.destroy', [$listing, $image]))
            ->assertForbidden();

        $this->assertDatabaseHas('listing_images', ['id' => $image->id]);
        Storage::disk('public')->assertExists($image->filename);
    }

    public function test_image_cannot_be_deleted_through_a_different_parent_listing(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['is_admin' => false]);
        $firstListing = Listing::factory()->for($owner, 'owner')->create();
        $secondListing = Listing::factory()->for($owner, 'owner')->create();
        $image = $secondListing->images()->create(['filename' => 'images/second-listing.jpg']);
        Storage::disk('public')->put($image->filename, 'image contents');

        $this
            ->actingAs($owner)
            ->delete(route('realtor.listing.image.destroy', [$firstListing, $image]))
            ->assertNotFound();

        $this->assertDatabaseHas('listing_images', ['id' => $image->id]);
        Storage::disk('public')->assertExists($image->filename);
    }

    public function test_unauthenticated_user_cannot_delete_a_listing_image(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['is_admin' => false]);
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $image = $listing->images()->create(['filename' => 'images/login-required.jpg']);
        Storage::disk('public')->put($image->filename, 'image contents');

        $this
            ->delete(route('realtor.listing.image.destroy', [$listing, $image]))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('listing_images', ['id' => $image->id]);
        Storage::disk('public')->assertExists($image->filename);
    }
}
