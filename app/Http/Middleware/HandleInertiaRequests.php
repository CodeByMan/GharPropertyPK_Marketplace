<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'developer' => 'Muhammad Ali Nawaz',
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'avatar_url' => $this->avatarUrl($user),
                'notificationCount' => $user->unreadNotifications()->count(),
            ] : null,
        ]);
    }

    private function avatarUrl(User $user): ?string
    {
        foreach (['avatar_url', 'profile_photo_url', 'photo_url'] as $attribute) {
            if (! $user->hasAttribute($attribute)) {
                continue;
            }

            $value = $user->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        foreach (['avatar', 'profile_photo_path'] as $attribute) {
            if (! $user->hasAttribute($attribute)) {
                continue;
            }

            $path = $user->getAttribute($attribute);

            if (is_string($path) && $path !== '') {
                if (str_starts_with($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }

                return Storage::disk('public')->url($path);
            }
        }

        return null;
    }
}
