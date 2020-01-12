<?php

declare(strict_types=1);

namespace Orchid\Tests\Feature\Platform;

use Orchid\Tests\TestFeatureCase;

class AuthTest extends TestFeatureCase
{
    public function testRouteDashboardLogin(): void
    {
        $response = $this->get(route('platform.login'));

        $response
            ->assertOk()
            ->assertSee('type="email"')
            ->assertSee('type="password"');
    }

    public function testRouteDashboardLoginAuth(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->get(route('platform.login'));

        $response
            ->assertStatus(302)
            ->assertRedirect('/home');
    }

    public function testRouteDashboardLoginAuthSuccess(): void
    {
        $response = $this->post(route('platform.login.auth'), [
            'email'    => $this->createAdminUser()->email,
            'password' => 'secret',
            'remember' => 'on',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect(route(config('platform.index')))
            ->assertCookieNotExpired('lockUser');
    }

    public function testRouteDashboardLoginAuthFail(): void
    {
        $response = $this->post(route('platform.login.auth'), [
            'email'    => $this->createAdminUser()->email,
            'password' => 'Incorrect password',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect('/');
    }

    public function testRouteDashboardPasswordRequest(): void
    {
        $response = $this->get(route('platform.password.request'));

        $response->assertOk()
            ->assertSee('type="email"')
            ->assertDontSee('type="password"');
    }

    public function testRouteDashboardPasswordReset(): void
    {
        $response = $this->get(route('platform.password.reset', '11111'));

        $response->assertOk()
            ->assertSee('type="email"')
            ->assertSee('type="password"')
            ->assertSee('"password_confirmation"');
    }

    public function testRouteDashboardPasswordResetAuth(): void
    {
        $response = $this->actingAs($this->createAdminUser())
            ->get(route('platform.password.reset', '11111'));

        $response
            ->assertStatus(302)
            ->assertRedirect('/home');
    }

    public function testRouteDashboardGuestLockAuth(): void
    {
        $response = $this->call('GET', route('platform.login.lock'), $parameters = [], $cookies = [
            'lockUser' => 1,
        ]);

        $response
            ->assertRedirect(route('platform.login'))
            ->assertCookieExpired('lockUser');
    }
}
