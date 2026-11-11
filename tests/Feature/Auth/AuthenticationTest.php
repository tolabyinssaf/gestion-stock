<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // 1. Login page rendering
    // -------------------------------------------------------

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('IT Manager Pro');
        $response->assertSee('Heureux de vous retrouver');
        $response->assertSee('Se connecter');
    }

    public function test_login_page_does_not_show_register_link(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('Créer un compte');
        $response->assertDontSee('Register');
    }

    // -------------------------------------------------------
    // 2. Successful authentication
    // -------------------------------------------------------

    public function test_admin_can_authenticate_and_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_responsable_it_can_authenticate_and_is_redirected_to_it_dashboard(): void
    {
        $user = User::factory()->responsableIt()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('it.dashboard'));
    }

    // -------------------------------------------------------
    // 3. Failed authentication
    // -------------------------------------------------------

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_nonexistent_email(): void
    {
        $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    // -------------------------------------------------------
    // 4. Field validation
    // -------------------------------------------------------

    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->post('/login', [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // -------------------------------------------------------
    // 5. Remember me
    // -------------------------------------------------------

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $this->assertAuthenticated();
        $response->assertCookie(auth()->guard()->getRecallerName());
    }

    // -------------------------------------------------------
    // 6. Role-based access control
    // -------------------------------------------------------

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Administrateur');
        $response->assertSee($user->name);
    }

    public function test_responsable_it_can_access_it_dashboard(): void
    {
        $user = User::factory()->responsableIt()->create();

        $response = $this->actingAs($user)->get('/it/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Responsable IT');
        $response->assertSee($user->name);
    }

    public function test_admin_cannot_access_it_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/it/dashboard');

        $response->assertStatus(403);
    }

    public function test_responsable_it_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->responsableIt()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    // -------------------------------------------------------
    // 7. Route protection for guests
    // -------------------------------------------------------

    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guests_cannot_access_it_dashboard(): void
    {
        $response = $this->get('/it/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guests_are_redirected_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    // -------------------------------------------------------
    // 8. Logout
    // -------------------------------------------------------

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    // -------------------------------------------------------
    // 9. Registration disabled
    // -------------------------------------------------------

    public function test_registration_route_is_not_available(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    // -------------------------------------------------------
    // 10. Root URL redirect
    // -------------------------------------------------------

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    // -------------------------------------------------------
    // 11. Dashboard redirect based on role
    // -------------------------------------------------------

    public function test_dashboard_redirects_admin_to_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_responsable_to_it_dashboard(): void
    {
        $user = User::factory()->responsableIt()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('it.dashboard'));
    }

    // -------------------------------------------------------
    // 12. User model roles
    // -------------------------------------------------------

    public function test_user_model_has_role_methods(): void
    {
        $admin = User::factory()->admin()->create();
        $responsable = User::factory()->responsableIt()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isResponsableIt());
        $this->assertTrue($admin->hasRole('admin'));

        $this->assertTrue($responsable->isResponsableIt());
        $this->assertFalse($responsable->isAdmin());
        $this->assertTrue($responsable->hasRole('responsable_it'));
    }

    public function test_user_model_has_role_labels(): void
    {
        $admin = User::factory()->admin()->create();
        $responsable = User::factory()->responsableIt()->create();

        $this->assertEquals('Administrateur', $admin->role_label);
        $this->assertEquals('Responsable IT', $responsable->role_label);
    }
}
