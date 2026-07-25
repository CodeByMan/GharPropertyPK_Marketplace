<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicListingPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listing_serializes_only_the_owner_identifier_required_by_the_interface(): void
    {
        $owner = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();

        $response = $this->get(route('listing.show', $listing));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listing/Show')
                ->where('listing.owner.id', $owner->id));

        $response->assertDontSee($owner->email);
    }
}
