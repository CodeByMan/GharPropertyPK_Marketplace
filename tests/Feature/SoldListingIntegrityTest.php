<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoldListingIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_cannot_delete_a_sold_listing_and_accepted_bidder_retains_access(): void
    {
        $owner = User::factory()->create();
        $acceptedBidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create(['sold_at' => now()]);
        Offer::query()->create([
            'listing_id' => $listing->id,
            'bidder_id' => $acceptedBidder->id,
            'amount' => 250000,
            'accepted_at' => now(),
        ]);

        $this->actingAs($owner)
            ->delete(route('realtor.listing.destroy', $listing))
            ->assertForbidden();

        $this->assertNull($listing->fresh()->deleted_at);

        $this->actingAs($acceptedBidder)
            ->get(route('listing.show', $listing))
            ->assertOk();
    }

    public function test_owner_can_still_delete_an_unsold_listing(): void
    {
        $owner = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)
            ->delete(route('realtor.listing.destroy', $listing))
            ->assertRedirect();

        $this->assertSoftDeleted('listings', ['id' => $listing->id]);
    }

    public function test_unauthorized_user_cannot_delete_an_unsold_or_sold_listing(): void
    {
        $owner = User::factory()->create();
        $unrelated = User::factory()->create();
        $unsold = Listing::factory()->for($owner, 'owner')->create();
        $sold = Listing::factory()->for($owner, 'owner')->create(['sold_at' => now()]);

        $this->actingAs($unrelated)
            ->delete(route('realtor.listing.destroy', $unsold))
            ->assertForbidden();

        $this->actingAs($unrelated)
            ->delete(route('realtor.listing.destroy', $sold))
            ->assertForbidden();

        $this->assertNull($unsold->fresh()->deleted_at);
        $this->assertNull($sold->fresh()->deleted_at);
    }
}
