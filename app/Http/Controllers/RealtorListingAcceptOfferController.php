<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Offer;
use App\Notifications\OfferAccepted;
use App\Notifications\OfferRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RealtorListingAcceptOfferController extends Controller
{
    public function __invoke(Offer $offer): RedirectResponse
    {
        $this->authorize('manage', $offer->listing);

        $listingId = $offer->listing_id;
        $offerId = $offer->id;

        DB::transaction(function () use ($listingId, $offerId): void {
            $listing = Listing::query()->lockForUpdate()->findOrFail($listingId);
            $selected = $listing->offers()->lockForUpdate()->findOrFail($offerId);

            if ($listing->sold_at !== null) {
                if ($selected->accepted_at !== null) {
                    return;
                }

                throw ValidationException::withMessages([
                    'offer' => 'This listing has already been sold.',
                ]);
            }

            $acceptedAt = now();
            $rejectedOffers = $listing->offers()
                ->whereKeyNot($selected->id)
                ->lockForUpdate()
                ->get();

            $listing->offers()->whereKeyNot($selected->id)->update([
                'accepted_at' => null,
                'rejected_at' => $acceptedAt,
            ]);

            $selected->update([
                'accepted_at' => $acceptedAt,
                'rejected_at' => null,
            ]);

            $listing->update(['sold_at' => $acceptedAt]);

            DB::afterCommit(function () use ($selected, $rejectedOffers): void {
                $selected->bidder->notify(new OfferAccepted($selected));
                foreach ($rejectedOffers as $rejectedOffer) {
                    $rejectedOffer->bidder->notify(new OfferRejected($rejectedOffer));
                }
            });
        }, 3);

        return redirect()->back()->with(
            'success',
            "Offer #{$offerId} accepted; competing offers were rejected."
        );
    }
}
