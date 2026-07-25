<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_users_are_not_administrators_by_default(): void
    {
        $this->assertFalse(User::factory()->make()->is_admin);
    }

    public function test_duplicate_offer_is_rejected(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $bidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        Offer::query()->create([
            'listing_id' => $listing->id,
            'bidder_id' => $bidder->id,
            'amount' => 100000,
        ]);

        $this->actingAs($bidder)
            ->post(route('listing.offer.store', $listing), ['amount' => 120000])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('offers', 1);
    }

    public function test_accepted_bidder_can_view_sold_listing(): void
    {
        $owner = User::factory()->create();
        $bidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create(['sold_at' => now()]);
        Offer::query()->create([
            'listing_id' => $listing->id,
            'bidder_id' => $bidder->id,
            'amount' => 100000,
            'accepted_at' => now(),
        ]);

        $this->actingAs($bidder)
            ->get(route('listing.show', $listing))
            ->assertOk();
    }

    public function test_login_is_rate_limited_after_five_failures(): void
    {
        RateLimiter::clear('nobody@example.com|127.0.0.1');

        foreach (range(1, 5) as $_) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => 'nobody@example.com',
                'password' => 'incorrect-password',
            ]);
        }

        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'nobody@example.com',
            'password' => 'incorrect-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_unknown_route_returns_404(): void
    {
        $this->get('/this-route-does-not-exist')->assertNotFound();
    }
}
