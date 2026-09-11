<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }

    public function test_a_person_can_register_with_a_unique_email(): void
    {
        $response = $this->post('/registro', [
            'first_name' => 'Pablo',
            'last_name' => 'García',
            'email' => 'pablo@example.test',
            'password' => 'Taskflow-2026',
            'password_confirmation' => 'Taskflow-2026',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'first_name' => 'Pablo',
            'last_name' => 'García',
            'email' => 'pablo@example.test',
        ]);
    }

    public function test_an_email_address_cannot_be_registered_twice(): void
    {
        User::factory()->create(['email' => 'equipo@example.test']);

        $this->from('/registro')->post('/registro', [
            'first_name' => 'Laura',
            'last_name' => 'Martín',
            'email' => 'equipo@example.test',
            'password' => 'Taskflow-2026',
            'password_confirmation' => 'Taskflow-2026',
        ])->assertRedirect('/registro')->assertSessionHasErrors('email');
    }

    public function test_a_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create([
            'email' => 'pablo@example.test',
            'password' => 'Taskflow-2026',
        ]);

        $this->post('/acceder', [
            'email' => 'PABLO@example.test',
            'password' => 'Taskflow-2026',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post('/salir')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_reset_request_does_not_reveal_if_account_exists(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'pablo@example.test']);

        $known = $this->post('/contrasena/olvidada', ['email' => $user->email]);
        $unknown = $this->post('/contrasena/olvidada', ['email' => 'nadie@example.test']);

        $known->assertSessionHas('status');
        $unknown->assertSessionHas('status', $known->getSession()->get('status'));
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_a_valid_reset_link_changes_password_and_invalidates_sessions(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'pablo@example.test']);

        DB::table('sessions')->insert([
            'id' => 'other-session',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $this->post('/contrasena/olvidada', ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use (&$token): bool {
                $token = $notification->token;

                return true;
            }
        );

        $this->post('/contrasena/restablecer', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NuevaClave-2026',
            'password_confirmation' => 'NuevaClave-2026',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NuevaClave-2026', $user->fresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'other-session']);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_an_authenticated_user_can_open_their_account_and_change_password(): void
    {
        $user = User::factory()->create(['password' => 'ClaveAnterior-2026']);

        DB::table('sessions')->insert([
            'id' => 'other-account-session',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($user)->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Mi cuenta')
            ->assertSee($user->email);

        $this->actingAs($user)->put(route('account.password.update'), [
            'current_password' => 'ClaveAnterior-2026',
            'password' => 'ClaveNueva-2026',
            'password_confirmation' => 'ClaveNueva-2026',
        ])->assertSessionHasNoErrors()->assertSessionHas('status');

        $this->assertTrue(Hash::check('ClaveNueva-2026', $user->fresh()->password));
        $this->assertFalse(Hash::check('ClaveAnterior-2026', $user->fresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'other-account-session']);
    }

    public function test_the_current_password_is_required_to_change_it(): void
    {
        $user = User::factory()->create(['password' => 'ClaveAnterior-2026']);

        $this->actingAs($user)->put(route('account.password.update'), [
            'current_password' => 'ContraseñaIncorrecta-2026',
            'password' => 'ClaveNueva-2026',
            'password_confirmation' => 'ClaveNueva-2026',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('ClaveAnterior-2026', $user->fresh()->password));
    }
}
