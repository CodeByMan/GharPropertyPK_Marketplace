<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\OfferAccepted;
use App\Notifications\OfferMade;
use App\Notifications\OfferRejected;
use Database\Seeders\Concerns\EnsuresDemoSeedingIsAllowed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoNotificationSeeder extends Seeder
{
    use EnsuresDemoSeedingIsAllowed;

    public function run(): void
    {
        $this->ensureDemoSeedingIsAllowed();

        $users = User::query()
            ->whereIn('email', array_keys(DemoMarketplaceData::USERS))
            ->get()
            ->keyBy('email');

        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->whereIn('notifiable_id', $users->pluck('id'))
            ->delete();

        foreach (DemoMarketplaceData::OFFERS as $index => $data) {
            $listingData = DemoMarketplaceData::LISTINGS[$data['listing']];
            $listing = Listing::withTrashed()
                ->where('city', $listingData['city'])
                ->where('street', $listingData['street'])
                ->where('street_nr', $listingData['street_nr'])
                ->firstOrFail();
            $bidder = $users->get($data['bidder']);
            $owner = $users->get($listingData['owner']);
            $offer = Offer::query()
                ->where('listing_id', $listing->id)
                ->where('bidder_id', $bidder->id)
                ->firstOrFail();

            $this->insertNotification(
                $owner,
                OfferMade::class,
                $offer,
                now()->subDays($data['created_days_ago']),
                $index % 3 === 0 ? now()->subDays(max(1, $data['created_days_ago'] - 1)) : null,
            );

            if ($data['status'] === 'accepted') {
                $this->insertNotification(
                    $bidder,
                    OfferAccepted::class,
                    $offer,
                    now()->subDays($data['decided_days_ago']),
                    null,
                );
            }

            if ($data['status'] === 'rejected') {
                $this->insertNotification(
                    $bidder,
                    OfferRejected::class,
                    $offer,
                    now()->subDays($data['decided_days_ago']),
                    $index % 2 === 0 ? now()->subDays(max(1, $data['decided_days_ago'] - 1)) : null,
                );
            }
        }
    }

    private function insertNotification(
        User $user,
        string $type,
        Offer $offer,
        $createdAt,
        $readAt,
    ): void {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'offer_id' => $offer->id,
                'listing_id' => $offer->listing_id,
                'amount' => $offer->amount,
                'bidder_id' => $offer->bidder_id,
            ], JSON_THROW_ON_ERROR),
            'read_at' => $readAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
