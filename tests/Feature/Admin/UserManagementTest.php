<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------
    // 1. Accès & Permissions
    // -------------------------------------------------------------

    public function test_admin_can_view_user_management_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Gestion des utilisateurs');
        $response->assertSee('Total Utilisateurs');
        $response->assertSee('Comptes Actifs');
        $response->assertSee($admin->name);
    }

    public function test_responsable_it_cannot_access_user_management_page(): void
    {
        $itUser = User::factory()->responsableIt()->create();

        $response = $this->actingAs($itUser)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------
    // 2. Création d'utilisateurs
    // -------------------------------------------------------------

    public function test_admin_can_create_a_new_responsable_it(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Jean IT',
            'email' => 'jean.it@entreprise.com',
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'password' => 'Secret2026!',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Jean IT',
            'email' => 'jean.it@entreprise.com',
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
        ]);

        $newUser = User::where('email', 'jean.it@entreprise.com')->first();
        $this->assertTrue(Hash::check('Secret2026!', $newUser->password));
    }

    public function test_admin_can_create_another_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Deuxième Admin',
            'email' => 'admin2@entreprise.com',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'password' => 'Admin2Pass!',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'admin2@entreprise.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_creating_user_requires_valid_data(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'role' => 'invalid-role',
            'status' => 'invalid-status',
            'password' => '123', // too short
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'role', 'status', 'password']);
    }

    public function test_creating_user_requires_unique_email(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'existing@entreprise.com']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Autre Nom',
            'email' => 'existing@entreprise.com',
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'password' => 'ValidPass123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    // -------------------------------------------------------------
    // 3. Modification d'utilisateurs
    // -------------------------------------------------------------

    public function test_admin_can_update_user_details(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->responsableIt()->create([
            'name' => 'Ancien Nom',
            'email' => 'ancien@entreprise.com',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'Nouveau Nom',
            'email' => 'nouveau@entreprise.com',
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'password' => '', // leave empty to keep existing password
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Nouveau Nom',
            'email' => 'nouveau@entreprise.com',
        ]);
    }

    public function test_admin_cannot_demote_the_last_active_admin(): void
    {
        // Only 1 admin in system
        $admin = User::factory()->admin()->active()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => User::ROLE_RESPONSABLE_IT, // try to demote self
            'status' => User::STATUS_ACTIVE,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(User::ROLE_ADMIN, $admin->fresh()->role);
    }

    // -------------------------------------------------------------
    // 4. Activation & Désactivation
    // -------------------------------------------------------------

    public function test_admin_can_deactivate_a_responsable_it(): void
    {
        $admin = User::factory()->admin()->create();
        $itUser = User::factory()->responsableIt()->active()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $itUser));

        $response->assertSessionHas('success');
        $this->assertEquals(User::STATUS_INACTIVE, $itUser->fresh()->status);
        $this->assertTrue($itUser->fresh()->isInactive());
    }

    public function test_admin_can_reactivate_an_inactive_user(): void
    {
        $admin = User::factory()->admin()->create();
        $itUser = User::factory()->responsableIt()->inactive()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $itUser));

        $response->assertSessionHas('success');
        $this->assertEquals(User::STATUS_ACTIVE, $itUser->fresh()->status);
        $this->assertTrue($itUser->fresh()->isActive());
    }

    public function test_admin_cannot_deactivate_the_last_active_admin(): void
    {
        // Only 1 admin in system
        $admin = User::factory()->admin()->active()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $admin));

        $response->assertSessionHas('error');
        $this->assertEquals(User::STATUS_ACTIVE, $admin->fresh()->status);
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $inactiveUser = User::factory()->responsableIt()->inactive()->create([
            'email' => 'inactive@entreprise.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'inactive@entreprise.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    // -------------------------------------------------------------
    // 5. Réinitialisation de mot de passe
    // -------------------------------------------------------------

    public function test_admin_can_reset_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->responsableIt()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.reset-password', $user), [
            'password' => 'NewPassword999!',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword999!', $user->fresh()->password));
    }

    // -------------------------------------------------------------
    // 6. Filtres et Recherche
    // -------------------------------------------------------------

    public function test_users_can_be_filtered_by_role_and_search(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Alice Admin', 'email' => 'alice@test.com']);
        $itUser = User::factory()->responsableIt()->create(['name' => 'Bob IT', 'email' => 'bob@test.com']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'Bob']));

        $response->assertStatus(200);
        $response->assertSee('Bob IT');
        $response->assertDontSee('Alice Admin');
    }
}
