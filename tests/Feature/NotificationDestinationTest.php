<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\OfferAccepted;
use App\Notifications\OfferMade;
use App\Notifications\OfferRejected;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationDestinationTest extends TestCase
{
    use RefreshDatabase;

    public function test_offer_notifications_use_role_appropriate_destinations(): void
    {
        $owner = User::factory()->create();
        $bidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $offer = Offer::query()->create([
            'listing_id' => $listing->id,
            'bidder_id' => $bidder->id,
            'amount' => 250000,
        ]);

        $this->assertSame(
            route('realtor.listing.show', $listing),
            (new OfferMade($offer))->toMail($owner)->actionUrl
        );
        $this->assertSame(
            route('listing.show', $listing),
            (new OfferAccepted($offer))->toMail($bidder)->actionUrl
        );
        $this->assertSame(
            route('listing.index'),
            (new OfferRejected($offer))->toMail($bidder)->actionUrl
        );
    }
}
