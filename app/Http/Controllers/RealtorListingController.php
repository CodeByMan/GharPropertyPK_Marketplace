<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Throwable;

class RealtorListingController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Listing::class);

        $validated = $request->validate([
            'deleted' => ['nullable', 'boolean'],
            'by' => ['nullable', Rule::in(['price', 'created_at'])],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $filters = [
            'deleted' => $request->boolean('deleted'),
            'by' => $validated['by'] ?? null,
            'order' => $validated['order'] ?? null,
        ];

        $user = $request->user();
        $query = Listing::query()
            ->when(! $user->is_admin, fn ($query) => $query->whereBelongsTo($user, 'owner'))
            ->with([
                'images' => fn ($query) => $query->oldest(),
                'owner:id,name',
            ])
            ->filter($filters)
            ->withCount(['images', 'offers']);

        if (! $filters['by']) {
            $query->mostRecent();
        }

        $listings = $query
            ->paginate(10)
            ->withQueryString()
            ->through(function (Listing $listing) use ($user): Listing {
                $listing->setAttribute('can', [
                    'manage' => $user->can('manage', $listing),
                    'update' => $user->can('update', $listing),
                    'delete' => $user->can('delete', $listing),
                    'restore' => $listing->trashed() && $user->can('restore', $listing),
                ]);

                return $listing;
            });

        return inertia('Realtor/Index', [
            'filters' => $filters,
            'isAdmin' => (bool) $user->is_admin,
            'listings' => $listings,
            'workspaceScope' => $user->is_admin ? 'all' : 'owned',
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Listing::class);

        return inertia('Realtor/Create');
    }

    public function store(StoreListingRequest $request): RedirectResponse
    {
        $this->authorize('create', Listing::class);

        $storedImagePath = null;

        try {
            DB::transaction(function () use ($request, &$storedImagePath): void {
                $listing = new Listing($request->safe()->except('image'));
                $listing->owner()->associate($request->user());
                $listing->save();

                if ($request->hasFile('image')) {
                    $storedImagePath = $request->file('image')->store('images', 'public');
                    $listing->images()->create(['filename' => $storedImagePath]);
                }
            });
        } catch (Throwable $exception) {
            if ($storedImagePath) {
                Storage::disk('public')->delete($storedImagePath);
            }

            throw $exception;
        }

        return redirect()->route('realtor.listing.index')
            ->with('success', 'Property was created successfully!');
    }

    public function details(Listing $listing): Response
    {
        $this->authorize('manage', $listing);
        $listing->load(['images', 'owner:id']);

        return inertia('Listing/Show', [
            'listing' => $listing,
            'managementMode' => true,
            'offerMade' => null,
        ]);
    }

    public function show(Listing $listing): Response
    {
        // The public `view` ability intentionally permits public browsing.
        // The realtor page contains private offer data and therefore requires
        // the separate seller-only `manage` ability.
        $this->authorize('manage', $listing);

        return inertia('Realtor/Show', [
            'listing' => $listing->load(['offers.bidder:id,name']),
        ]);
    }

    public function edit(Listing $listing): Response
    {
        $this->authorize('update', $listing);

        return inertia('Realtor/Edit', ['listing' => $listing]);
    }

    public function update(UpdateListingRequest $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $listing->update($request->validated());

        return redirect()->route('realtor.listing.index')
            ->with('success', 'Property was updated successfully!');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        $listing->deleteOrFail();

        return redirect()->back()->with('success', 'Property was deleted successfully!');
    }

    public function restore(Listing $listing): RedirectResponse
    {
        // Custom resource actions are not covered by authorizeResource().
        $this->authorize('restore', $listing);

        $listing->restore();

        return redirect()->back()->with('success', 'Property was restored successfully!');
    }
}
