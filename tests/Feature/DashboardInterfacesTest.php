<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInterfacesTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------
    // 1. Interface Administrateur
    // -------------------------------------------------------------

    public function test_admin_can_view_admin_dashboard_with_real_metrics(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Sophie Admin']);
        User::factory()->count(3)->responsableIt()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Bonjour, Sophie Admin');
        $response->assertSee('Administrateur');
        $response->assertSee('Statistiques des comptes');
        $response->assertSee('Utilisateurs enregistrés');
        $response->assertSee('Accès rapides d\'administration');
    }

    public function test_admin_sidebar_displays_expected_navigation_items(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Required Admin sections and items
        $response->assertSee('Principal');
        $response->assertSee('Tableau de bord');
        $response->assertSee('Administration');
        $response->assertSee('Utilisateurs');
        $response->assertSee('Rôles et permissions');
        $response->assertSee('Supervision');
        $response->assertSee('Journal des activités');
        $response->assertSee('Configuration');
        $response->assertSee('Paramètres');
        $response->assertSee('Compte');
        $response->assertSee('Mon profil');
        $response->assertSee('Déconnexion');

        // Must NOT display stock management modules in Admin sidebar
        $response->assertDontSee('Catalogue de matériel');
        $response->assertDontSee('Gestion des Stocks');
        $response->assertDontSee('État du stock');
        $response->assertDontSee('Entrées de stock');
        $response->assertDontSee('Sorties de stock');
        $response->assertDontSee('Historique des mouvements');
    }

    // -------------------------------------------------------------
    // 2. Interface Responsable IT
    // -------------------------------------------------------------

    public function test_it_manager_can_view_it_dashboard_with_all_sections(): void
    {
        $itUser = User::factory()->responsableIt()->create(['name' => 'Marc IT']);

        $response = $this->actingAs($itUser)->get('/it/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Bonjour, Marc IT');
        $response->assertSee('Responsable IT');

        // Required 6 Stat Cards
        $response->assertSee('Total des équipements');
        $response->assertSee('Équipements disponibles');
        $response->assertSee('Équipements en utilisation');
        $response->assertSee('Équipements en panne');
        $response->assertSee('Équipements hors service');
        $response->assertSee('Produits en stock faible');

        // Required Quick Actions
        $response->assertSee('Actions rapides');
        $response->assertSee('Ajouter un produit');
        $response->assertSee('Enregistrer une entrée');
        $response->assertSee('Enregistrer une sortie');
        $response->assertSee('Préparer une demande');

        // Required Tables with Empty States
        $response->assertSee('Derniers mouvements');
        $response->assertSee('Aucun mouvement enregistré');
        $response->assertSee('Alertes de stock');
        $response->assertSee('Aucune alerte de stock');
    }

    public function test_it_sidebar_displays_expected_navigation_items(): void
    {
        $itUser = User::factory()->responsableIt()->create();

        $response = $this->actingAs($itUser)->get('/it/dashboard');

        $response->assertStatus(200);

        // Required IT sections and items
        $response->assertSee('Principal');
        $response->assertSee('Tableau de bord');
        $response->assertSee('Parc Informatique');
        $response->assertSee('Catalogue de matériel');
        $response->assertSee('Équipements');
        $response->assertSee('Catégories');
        $response->assertSee('Emplacements');
        $response->assertSee('Gestion des Stocks');
        $response->assertSee('État du stock');
        $response->assertSee('Entrées de stock');
        $response->assertSee('Sorties de stock');
        $response->assertSee('Historique des mouvements');
        $response->assertSee('Demandes');
        $response->assertSee('Nouvelle demande');
        $response->assertSee('Historique des demandes');
        $response->assertSee('Analyse');
        $response->assertSee('Rapports et exports');
        $response->assertSee('Compte');
        $response->assertSee('Mon profil');
        $response->assertSee('Déconnexion');

        // Must NOT display Admin specific modules in IT sidebar
        $response->assertDontSee('Rôles et permissions');
        $response->assertDontSee('Journal des activités');

        // Must NOT display excluded modules
        $response->assertDontSee('Gestion des fournisseurs');
        $response->assertDontSee('Commandes d\'achat');
        $response->assertDontSee('Factures');
        $response->assertDontSee('Gestion des employés');
        $response->assertDontSee('Affectations aux employés');
        $response->assertDontSee('QR codes');
        $response->assertDontSee('Notifications internes');
    }

    // -------------------------------------------------------------
    // 3. Isolation stricte des rôles
    // -------------------------------------------------------------

    public function test_admin_cannot_access_it_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/it/dashboard');

        $response->assertStatus(403);
    }

    public function test_responsable_it_cannot_access_admin_dashboard(): void
    {
        $itUser = User::factory()->responsableIt()->create();

        $response = $this->actingAs($itUser)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect(route('login'));

        $responseIt = $this->get('/it/dashboard');
        $responseIt->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------
    // 4. Header et Profil
    // -------------------------------------------------------------

    public function test_header_displays_authenticated_user_info(): void
    {
        $user = User::factory()->admin()->create(['name' => 'Alice Martin']);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Alice Martin');
        $response->assertSee('AM'); // Initials
    }

    public function test_authenticated_user_can_access_profile_page(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Mon profil');
    }
}
