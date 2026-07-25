<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function before(?User $user, string $ability): ?bool
    {
        if (! $user?->is_admin) {
            return null;
        }

        return in_array($ability, [
            'manage',
            'update',
            'delete',
            'restore',
            'forceDelete',
        ], true) ? true : null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Listing $listing): bool
    {
        if ($listing->sold_at === null || $listing->user_id === $user?->id) {
            return true;
        }

        return $user !== null && $listing->offers()
            ->where('bidder_id', $user->id)
            ->whereNotNull('accepted_at')
            ->exists();
    }

    /**
     * Manage the seller-only realtor page, including private offer data.
     */
    public function manage(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Listing $listing): bool
    {
        return $listing->sold_at === null && $listing->user_id === $user->id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $listing->sold_at === null && $listing->user_id === $user->id;
    }

    public function restore(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    public function forceDelete(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    public function offer(User $user, Listing $listing): bool
    {
        return $listing->sold_at === null && $listing->user_id !== $user->id;
    }
}
