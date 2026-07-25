<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrowseListingsRequest;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /**
     * Authorize listing resource.
     */
    public function __construct()
    {
        $this->authorizeResource(Listing::class, 'listing');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BrowseListingsRequest $request)
    {
        $filters = $request->validated();

        return inertia('Listing/Index', [
            'filters' => $filters,
            'listings' => Listing::query()
                ->with(['images' => fn ($query) => $query->oldest()])
                ->mostRecent()
                ->filter($filters)
                ->withoutSold()
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Listing $listing)
    {
        $listing->load(['images', 'owner:id']);
        $user = $request->user();
        $offer = $user
            ? $listing->offers()->where('bidder_id', $user->id)->first()
            : null;

        return inertia('Listing/Show', [
            'listing' => $listing,
            'offerMade' => $offer,
        ]);
    }
}
