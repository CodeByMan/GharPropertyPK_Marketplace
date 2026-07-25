<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Notifications\OfferMade;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ListingOfferController extends Controller
{
    public function store(Listing $listing, Request $request)
    {
        $this->authorize('offer', $listing);

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:4294967295'],
        ]);

        try {
            $offer = $listing->offers()->create([
                'bidder_id' => $request->user()->id,
                'amount' => $validated['amount'],
            ]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'amount' => 'You have already submitted an offer for this listing.',
            ]);
        }

        $listing->owner->notify(new OfferMade($offer));

        return redirect()->back()->with('success', 'Your offer has been submitted!');
    }
}
