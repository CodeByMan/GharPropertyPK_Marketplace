<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RealtorListingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unrelated_user_can_view_an_unsold_public_listing_but_not_its_realtor_offer_page(): void
    {
        $owner = User::factory()->create();
        $unrelated = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        $this->actingAs($unrelated)
            ->get(route('listing.show', $listing))
            ->assertOk();

        $this->actingAs($unrelated)
            ->get(route('realtor.listing.show', $listing))
            ->assertForbidden();
    }

    public function test_accepted_bidder_cannot_access_the_sellers_offer_management_page(): void
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

        $this->actingAs($acceptedBidder)
            ->get(route('listing.show', $listing))
            ->assertOk();

        $this->actingAs($acceptedBidder)
            ->get(route('realtor.listing.show', $listing))
            ->assertForbidden();
    }

    public function test_owner_can_view_all_offers_without_serializing_bidder_email_addresses(): void
    {
        $owner = User::factory()->create();
        $firstBidder = User::factory()->create();
        $secondBidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        foreach ([$firstBidder, $secondBidder] as $index => $bidder) {
            Offer::query()->create([
                'listing_id' => $listing->id,
                'bidder_id' => $bidder->id,
                'amount' => 200000 + ($index * 10000),
            ]);
        }

        $response = $this->actingAs($owner)
            ->get(route('realtor.listing.show', $listing));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Realtor/Show')
                ->where('listing.id', $listing->id)
                ->has('listing.offers', 2)
                ->where('listing.offers.0.bidder.name', $firstBidder->name));

        $response->assertDontSee($firstBidder->email);
        $response->assertDontSee($secondBidder->email);
    }

    public function test_guest_is_redirected_from_the_offer_management_page(): void
    {
        $listing = Listing::factory()->create();

        $this->get(route('realtor.listing.show', $listing))
            ->assertRedirect(route('login'));
    }

    public function test_administrator_can_view_another_sellers_offer_management_page(): void
    {
        $owner = User::factory()->create();
        $administrator = User::factory()->admin()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        $this->actingAs($administrator)
            ->get(route('realtor.listing.show', $listing))
            ->assertOk();
    }

    public function test_owner_can_restore_their_deleted_listing(): void
    {
        $owner = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $listing->delete();

        $this->actingAs($owner)
            ->put(route('realtor.listing.restore', $listing))
            ->assertRedirect();

        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unrelated_user_cannot_restore_another_sellers_listing(): void
    {
        $owner = User::factory()->create();
        $unrelated = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $listing->delete();

        $this->actingAs($unrelated)
            ->put(route('realtor.listing.restore', $listing))
            ->assertForbidden();

        $this->assertNotNull(Listing::withTrashed()->findOrFail($listing->id)->deleted_at);
    }

    public function test_guest_cannot_restore_a_deleted_listing(): void
    {
        $listing = Listing::factory()->create();
        $listing->delete();

        $this->put(route('realtor.listing.restore', $listing))
            ->assertRedirect(route('login'));
    }

    public function test_administrator_can_restore_another_sellers_listing(): void
    {
        $administrator = User::factory()->admin()->create();
        $listing = Listing::factory()->create();
        $listing->delete();

        $this->actingAs($administrator)
            ->put(route('realtor.listing.restore', $listing))
            ->assertRedirect();

        $this->assertNull($listing->fresh()->deleted_at);
    }

    public function test_administrator_workspace_loads_all_active_database_properties(): void
    {
        $administrator = User::factory()->admin()->create();
        $firstOwner = User::factory()->create();
        $secondOwner = User::factory()->create();
        Listing::factory()->for($firstOwner, 'owner')->create();
        Listing::factory()->for($secondOwner, 'owner')->create();

        $this->actingAs($administrator)
            ->get(route('realtor.listing.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Realtor/Index')
                ->where('isAdmin', true)
                ->where('workspaceScope', 'all')
                ->has('listings.data', 2));
    }

    public function test_regular_user_workspace_loads_only_their_own_properties(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $ownedListing = Listing::factory()->for($owner, 'owner')->create();
        Listing::factory()->for($otherOwner, 'owner')->create();

        $this->actingAs($owner)
            ->get(route('realtor.listing.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Realtor/Index')
                ->where('isAdmin', false)
                ->where('workspaceScope', 'owned')
                ->has('listings.data', 1)
                ->where('listings.data.0.id', $ownedListing->id));
    }

    public function test_owner_can_open_the_property_details_view_from_their_workspace(): void
    {
        $owner = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)
            ->get(route('realtor.listing.details', $listing))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing/Show')
                ->where('managementMode', true)
                ->where('listing.id', $listing->id));
    }

    public function test_administrator_can_open_deleted_property_details(): void
    {
        $administrator = User::factory()->admin()->create();
        $listing = Listing::factory()->create();
        $listing->delete();

        $this->actingAs($administrator)
            ->get(route('realtor.listing.details', $listing))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing/Show')
                ->where('managementMode', true)
                ->where('listing.id', $listing->id));
    }
}
