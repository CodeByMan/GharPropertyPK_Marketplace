<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Concerns\EnsuresDemoSeedingIsAllowed;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    use EnsuresDemoSeedingIsAllowed;

    private const LEGACY_EMAILS = [
        'seller@gharmarkaz.test' => 'seller@gharproperty.test',
        'agent@gharmarkaz.test' => 'agent@gharproperty.test',
        'buyer@gharmarkaz.test' => 'buyer@gharproperty.test',
        'investor@gharmarkaz.test' => 'investor@gharproperty.test',
    ];

    public function run(): void
    {
        $password = $this->ensureDemoSeedingIsAllowed();

        foreach (self::LEGACY_EMAILS as $legacyEmail => $currentEmail) {
            if (! User::query()->where('email', $currentEmail)->exists()) {
                User::query()->where('email', $legacyEmail)->update(['email' => $currentEmail]);
            }
        }

        foreach (DemoMarketplaceData::USERS as $email => $attributes) {
            $user = User::query()->firstOrNew(['email' => $email]);

            $user->forceFill([
                'name' => $attributes['name'],
                'email_verified_at' => now(),
                'password' => $password,
                'is_admin' => $attributes['is_admin'],
            ])->save();
        }
    }
}
