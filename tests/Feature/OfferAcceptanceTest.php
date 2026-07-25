<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\OfferAccepted;
use App\Notifications\OfferRejected;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class OfferAcceptanceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_only_owner_can_accept_an_offer(): void
    {
        $owner = User::factory()->create();
        $unrelated = User::factory()->create();
        $bidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $offer = $this->offer($listing, $bidder, 210000);

        $this->actingAs($unrelated)
            ->put(route('realtor.offer.accept', $offer))
            ->assertForbidden();

        $this->assertNull($listing->fresh()->sold_at);
        $this->assertNull($offer->fresh()->accepted_at);
    }

    public function test_accepting_an_offer_sells_the_listing_and_rejects_all_competitors_atomically(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $acceptedBidder = User::factory()->create();
        $rejectedBidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $accepted = $this->offer($listing, $acceptedBidder, 250000);
        $rejected = $this->offer($listing, $rejectedBidder, 240000);

        $this->actingAs($owner)
            ->put(route('realtor.offer.accept', $accepted))
            ->assertRedirect();

        $this->assertNotNull($listing->fresh()->sold_at);
        $this->assertNotNull($accepted->fresh()->accepted_at);
        $this->assertNull($accepted->fresh()->rejected_at);
        $this->assertNull($rejected->fresh()->accepted_at);
        $this->assertNotNull($rejected->fresh()->rejected_at);
        $this->assertSame(1, $listing->offers()->whereNotNull('accepted_at')->count());

        Notification::assertSentTo($acceptedBidder, OfferAccepted::class);
        Notification::assertSentTo($rejectedBidder, OfferRejected::class);
    }

    public function test_replaying_the_same_acceptance_is_idempotent_and_does_not_duplicate_notifications(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $acceptedBidder = User::factory()->create();
        $rejectedBidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $accepted = $this->offer($listing, $acceptedBidder, 250000);
        $this->offer($listing, $rejectedBidder, 240000);

        $this->actingAs($owner)->put(route('realtor.offer.accept', $accepted))->assertRedirect();
        $soldAt = $listing->fresh()->sold_at;

        $this->actingAs($owner)->put(route('realtor.offer.accept', $accepted))->assertRedirect();

        $this->assertTrue($soldAt->equalTo($listing->fresh()->sold_at));
        $this->assertCount(1, Notification::sent($acceptedBidder, OfferAccepted::class));
        $this->assertCount(1, Notification::sent($rejectedBidder, OfferRejected::class));
        $this->assertSame(1, $listing->offers()->whereNotNull('accepted_at')->count());
    }

    public function test_notification_failure_cannot_leave_partially_updated_offer_state(): void
    {
        $owner = User::factory()->create();
        $acceptedBidder = User::factory()->create();
        $rejectedBidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $accepted = $this->offer($listing, $acceptedBidder, 250000);
        $rejected = $this->offer($listing, $rejectedBidder, 240000);

        $this->mock(Dispatcher::class)
            ->shouldReceive('send')
            ->andThrow(new RuntimeException('Notification transport failed.'));

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($owner)->put(route('realtor.offer.accept', $accepted));
            $this->fail('The simulated notification exception was not thrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Notification transport failed.', $exception->getMessage());
        }

        $this->assertNotNull($listing->fresh()->sold_at);
        $this->assertNotNull($accepted->fresh()->accepted_at);
        $this->assertNull($accepted->fresh()->rejected_at);
        $this->assertNull($rejected->fresh()->accepted_at);
        $this->assertNotNull($rejected->fresh()->rejected_at);
        $this->assertSame(1, $listing->offers()->whereNotNull('accepted_at')->count());
    }

    public function test_a_different_offer_cannot_be_accepted_after_the_listing_is_sold(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $firstBidder = User::factory()->create();
        $secondBidder = User::factory()->create();
        $listing = Listing::factory()->for($owner, 'owner')->create();
        $first = $this->offer($listing, $firstBidder, 250000);
        $second = $this->offer($listing, $secondBidder, 260000);

        $this->actingAs($owner)->put(route('realtor.offer.accept', $first))->assertRedirect();

        $this->actingAs($owner)
            ->put(route('realtor.offer.accept', $second))
            ->assertSessionHasErrors('offer');

        $this->assertNotNull($first->fresh()->accepted_at);
        $this->assertNull($second->fresh()->accepted_at);
        $this->assertNotNull($second->fresh()->rejected_at);
        $this->assertSame(1, $listing->offers()->whereNotNull('accepted_at')->count());
    }

    private function offer(Listing $listing, User $bidder, int $amount): Offer
    {
        return Offer::query()->create([
            'listing_id' => $listing->id,
            'bidder_id' => $bidder->id,
            'amount' => $amount,
        ]);
    }
}
