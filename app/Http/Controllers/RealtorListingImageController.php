<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingImagesRequest;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Throwable;

class RealtorListingImageController extends Controller
{
    private const MAX_IMAGES_PER_LISTING = 20;

    public function create(Listing $listing): Response
    {
        $this->authorize('update', $listing);
        $listing->load('images');

        return inertia('Realtor/ListingImage/Create', ['listing' => $listing]);
    }

    public function store(Listing $listing, StoreListingImagesRequest $request): RedirectResponse
    {
        $this->authorize('update', $listing);
        $files = $request->file('images', []);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($listing, $files, &$storedPaths): void {
                $lockedListing = Listing::query()
                    ->lockForUpdate()
                    ->findOrFail($listing->getKey());

                if ($lockedListing->images()->count() + count($files) > self::MAX_IMAGES_PER_LISTING) {
                    throw ValidationException::withMessages([
                        'images' => 'A listing may contain no more than 20 images.',
                    ]);
                }

                foreach ($files as $file) {
                    $path = $file->store('images', 'public');
                    $storedPaths[] = $path;
                    $lockedListing->images()->create(['filename' => $path]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        return redirect()->back()->with('success', 'Images uploaded!');
    }

    public function destroy(Listing $listing, ListingImage $image): RedirectResponse
    {
        $this->authorize('update', $listing);

        DB::transaction(function () use ($image): void {
            $filename = $image->filename;
            $image->delete();
            DB::afterCommit(fn () => Storage::disk('public')->delete($filename));
        });

        return redirect()->back()->with('success', 'Image was deleted!');
    }
}
