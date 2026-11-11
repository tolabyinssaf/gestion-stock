<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\User;
use App\Services\ActivityLoggerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $itUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'name' => 'Super Administrateur',
            'email' => 'admin@test.local',
        ]);

        $this->itUser = User::factory()->create([
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'name' => 'Responsable IT Test',
            'email' => 'respo@test.local',
        ]);
    }

    public function test_admin_can_access_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Paramètres de l\'application');
        $response->assertSee('Destinataire des demandes de matériel');
    }

    public function test_responsable_it_forbidden_from_admin_settings(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('admin.settings.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_update_settings_and_activity_is_logged(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('admin.settings.update'), [
            'nom_application' => 'IT Manager Pro V2',
            'nom_entreprise' => 'Acme Corporation',
            'directeur_nom' => 'M. Martin Dupont',
            'directeur_email' => 'martin.dupont@acme.com',
            'signature_service_it' => 'Département IT Acme',
            'lignes_par_page' => 20,
            'format_date' => 'd/m/Y',
            'fuseau_horaire' => 'Europe/Paris',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('IT Manager Pro V2', AppSetting::get('nom_application'));
        $this->assertEquals('martin.dupont@acme.com', AppSetting::get('directeur_email'));
        $this->assertEquals('Acme Corporation', AppSetting::get('nom_entreprise'));

        // Check activity log recorded
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'parametres',
            'action_type' => 'modification',
            'user_id' => $this->adminUser->id,
        ]);
    }

    public function test_activity_logger_service_never_records_passwords(): void
    {
        $logger = app(ActivityLoggerService::class);
        $log = $logger->log(
            module: 'utilisateurs',
            actionType: 'creation',
            description: 'Test création utilisateur',
            entityType: 'User',
            entityId: 99,
            entityName: 'Test Account',
            oldValues: ['password' => 'secret123', 'email' => 'old@test.com'],
            newValues: ['password' => 'secret456', 'password_confirmation' => 'secret456', 'email' => 'new@test.com']
        );

        $this->assertArrayNotHasKey('password', $log->old_values ?? []);
        $this->assertArrayNotHasKey('password', $log->new_values ?? []);
        $this->assertArrayNotHasKey('password_confirmation', $log->new_values ?? []);
        $this->assertEquals('new@test.com', $log->new_values['email']);
    }

    public function test_admin_can_view_activity_logs(): void
    {
        ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'user_name' => $this->adminUser->name,
            'action_type' => 'creation',
            'module' => 'utilisateurs',
            'description' => 'Test log entry visible',
            'entity_name' => 'Jean Collaborateur',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.activities.index'));
        $response->assertStatus(200);
        $response->assertSee('Journal d\'activités & Audit de sécurité');
        $response->assertSee('Test log entry visible');
    }

    public function test_responsable_it_forbidden_from_activity_logs(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('admin.activities.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_single_activity_detail(): void
    {
        $log = ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'user_name' => $this->adminUser->name,
            'action_type' => 'modification',
            'module' => 'stock',
            'description' => 'Ajustement de stock',
            'old_values' => ['quantite' => 5],
            'new_values' => ['quantite' => 8],
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.activities.show', $log));
        $response->assertStatus(200);
        $response->assertSee('Détail de l\'opération #' . $log->id);
        $response->assertSee('Ajustement de stock');
        $response->assertSee('quantite:');
    }

    public function test_user_actions_generate_audit_logs(): void
    {
        // 1. Create a user via Admin UserController
        $this->actingAs($this->adminUser)->post(route('admin.users.store'), [
            'name' => 'Alice Martin',
            'email' => 'alice@test.local',
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'password' => 'SecretPass123!',
            'password_confirmation' => 'SecretPass123!',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'utilisateurs',
            'action_type' => 'creation',
            'entity_name' => 'Alice Martin',
        ]);

        $createdUser = User::where('email', 'alice@test.local')->first();
        $this->assertNotNull($createdUser);

        // 2. Toggle status
        $this->actingAs($this->adminUser)->patch(route('admin.users.toggle-status', $createdUser));
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'utilisateurs',
            'action_type' => 'desactivation',
            'entity_name' => 'Alice Martin',
        ]);
    }
}
