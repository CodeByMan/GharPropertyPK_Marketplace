<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Str;
use RuntimeException;

trait EnsuresDemoSeedingIsAllowed
{
    protected function ensureDemoSeedingIsAllowed(): string
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo seeding is allowed only in local and testing environments.');
        }

        if (! config('app.allow_demo_seeding')) {
            throw new RuntimeException('Set ALLOW_DEMO_SEEDING=true to create demo data.');
        }

        $password = (string) config('app.demo_password');

        if (Str::length($password) < 12) {
            throw new RuntimeException('DEMO_PASSWORD must contain at least 12 characters.');
        }

        return $password;
    }
}
