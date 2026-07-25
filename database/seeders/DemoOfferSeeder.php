<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Database\Seeders\Concerns\EnsuresDemoSeedingIsAllowed;
use Illuminate\Database\Seeder;

class DemoOfferSeeder extends Seeder
{
    use EnsuresDemoSeedingIsAllowed;

    public function run(): void
    {
        $this->ensureDemoSeedingIsAllowed();

        $listings = collect(DemoMarketplaceData::LISTINGS)
            ->mapWithKeys(fn (array $data, string $key) => [
                $key => Listing::withTrashed()
                    ->where('city', $data['city'])
                    ->where('street', $data['street'])
                    ->where('street_nr', $data['street_nr'])
                    ->firstOrFail(),
            ]);

        Offer::query()->whereIn('listing_id', $listings->pluck('id'))->delete();

        foreach (DemoMarketplaceData::OFFERS as $data) {
            $listing = $listings->get($data['listing']);
            $bidder = User::query()->where('email', $data['bidder'])->firstOrFail();
            $decisionAt = isset($data['decided_days_ago'])
                ? now()->subDays($data['decided_days_ago'])
                : null;

            $offer = Offer::query()->create([
                'listing_id' => $listing->id,
                'bidder_id' => $bidder->id,
                'amount' => $data['amount'],
                'accepted_at' => $data['status'] === 'accepted' ? $decisionAt : null,
                'rejected_at' => $data['status'] === 'rejected' ? $decisionAt : null,
            ]);

            $offer->forceFill([
                'created_at' => now()->subDays($data['created_days_ago']),
                'updated_at' => $decisionAt ?? now()->subDays($data['created_days_ago']),
            ])->saveQuietly();
        }
    }
}
