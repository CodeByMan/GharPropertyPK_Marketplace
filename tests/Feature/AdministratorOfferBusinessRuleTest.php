<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdministratorOfferBusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_cannot_offer_on_their_own_listing(): void
    {
        Notification::fake();

        $administrator = User::factory()->admin()->create();
        $listing = Listing::factory()->for($administrator, 'owner')->create();

        $this->actingAs($administrator)
            ->post(route('listing.offer.store', $listing), ['amount' => 250000])
            ->assertForbidden();

        $this->assertDatabaseCount('offers', 0);
    }

    public function test_administrator_cannot_offer_on_a_sold_listing(): void
    {
        Notification::fake();

        $administrator = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $listing = Listing::factory()
            ->for($owner, 'owner')
            ->create(['sold_at' => now()]);

        $this->actingAs($administrator)
            ->post(route('listing.offer.store', $listing), ['amount' => 250000])
            ->assertForbidden();

        $this->assertDatabaseCount('offers', 0);
    }
}
