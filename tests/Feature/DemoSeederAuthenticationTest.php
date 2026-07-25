<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DemoSeederAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_explicitly_enabled_local_seeding_creates_a_working_administrator_login(): void
    {
        $password = 'StrongDemoPassword!2026';

        Storage::fake('public');
        $this->app['env'] = 'local';

        config([
            'app.allow_demo_seeding' => true,
            'app.demo_password' => $password,
        ]);

        $this->runDemoSeederDirectly();

        $administrator = User::query()
            ->where('email', 'admin@example.com')
            ->firstOrFail();

        $this->assertTrue($administrator->is_admin);
        $this->assertTrue(
            Hash::check($password, $administrator->password)
        );

        $this->app['env'] = 'testing';

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->post(route('login.store'), [
            'email' => $administrator->email,
            'password' => $password,
        ])->assertRedirect(route('listing.index'));

        $this->assertAuthenticatedAs($administrator);
    }

    public function test_production_refuses_demo_seeding_even_with_opt_in(): void
    {
        $this->app['env'] = 'production';

        config([
            'app.allow_demo_seeding' => true,
            'app.demo_password' => 'StrongDemoPassword!2026',
        ]);

        $this->expectException(RuntimeException::class);

        $this->runDemoSeederDirectly();
    }

    public function test_staging_refuses_demo_seeding_even_with_opt_in(): void
    {
        $this->app['env'] = 'staging';

        config([
            'app.allow_demo_seeding' => true,
            'app.demo_password' => 'StrongDemoPassword!2026',
        ]);

        $this->expectException(RuntimeException::class);

        $this->runDemoSeederDirectly();
    }

    public function test_local_seeding_requires_explicit_opt_in(): void
    {
        $this->app['env'] = 'local';

        config([
            'app.allow_demo_seeding' => false,
            'app.demo_password' => 'StrongDemoPassword!2026',
        ]);

        $this->expectException(RuntimeException::class);

        $this->runDemoSeederDirectly();
    }

    public function test_weak_demo_password_is_rejected(): void
    {
        $this->app['env'] = 'local';

        config([
            'app.allow_demo_seeding' => true,
            'app.demo_password' => 'short',
        ]);

        $this->expectException(RuntimeException::class);

        $this->runDemoSeederDirectly();
    }

    private function runDemoSeederDirectly(): void
    {
        $this->app
            ->make(DatabaseSeeder::class)
            ->run();
    }
}
