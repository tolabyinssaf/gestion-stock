<?php

namespace Tests\Feature\IT;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\DemandeMateriel;
use App\Models\Produit;
use App\Models\User;
use App\Services\DemandeGmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemandeMaterielTest extends TestCase
{
    use RefreshDatabase;

    protected User $itUser;
    protected User $adminUser;
    protected Produit $produitActif;
    protected Produit $produitInactif;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itUser = User::factory()->create([
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'name' => 'Responsable IT Test',
        ]);

        $this->adminUser = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        // Configure director email
        AppSetting::set('directeur_nom', 'Direction Générale', 'email');
        AppSetting::set('directeur_email', 'direction@entreprise.com', 'email');

        $category = Category::create([
            'nom' => 'Ordinateurs portables',
            'code' => 'PC-PORT',
            'est_actif' => true,
        ]);

        $this->produitActif = Produit::create([
            'designation' => 'Dell Latitude 5540',
            'reference' => 'PC-LAT-5540',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
            'seuil_minimum' => 3,
            'category_id' => $category->id,
        ]);

        $this->produitInactif = Produit::create([
            'designation' => 'Ancien Modèle Archivé',
            'reference' => 'PC-OLD-001',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_INACTIF,
            'seuil_minimum' => 0,
            'category_id' => $category->id,
        ]);
    }

    public function test_responsable_it_can_access_demandes_index(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('it.demandes.index'));
        $response->assertStatus(200);
        $response->assertSee('Demandes de matériel par Gmail');
    }

    public function test_admin_cannot_access_it_demandes(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('it.demandes.index'));
        $response->assertStatus(403);
    }

    public function test_creation_brouillon_demande_materiel(): void
    {
        $response = $this->actingAs($this->itUser)->post(route('it.demandes.store'), [
            'produit_id' => $this->produitActif->id,
            'quantite' => 2,
            'priorite' => 'importante',
            'motif' => 'Arrivée de nouveaux développeurs',
            'commentaire' => 'Prévoir livraison avant la fin du mois',
            'action_type' => 'save_draft',
        ]);

        $this->assertDatabaseHas('demandes_materiel', [
            'produit_id' => $this->produitActif->id,
            'quantite' => 2,
            'priorite' => 'importante',
            'statut_preparation' => DemandeMateriel::STATUT_BROUILLON,
            'motif' => 'Arrivée de nouveaux développeurs',
        ]);

        $demande = DemandeMateriel::where('produit_id', $this->produitActif->id)->first();
        $this->assertNotNull($demande);
        $this->assertStringStartsWith('DEM-', $demande->reference);
        $response->assertRedirect(route('it.demandes.show', $demande));
    }

    public function test_creation_demande_prete_pour_gmail(): void
    {
        $response = $this->actingAs($this->itUser)->post(route('it.demandes.store'), [
            'produit_id' => $this->produitActif->id,
            'quantite' => 5,
            'priorite' => 'urgente',
            'motif' => 'Remplacement suite à un dégât des eaux',
            'action_type' => 'prepare_gmail',
        ]);

        $demande = DemandeMateriel::where('quantite', 5)->first();
        $this->assertNotNull($demande);
        $this->assertEquals(DemandeMateriel::STATUT_PRETE, $demande->statut_preparation);
        $this->assertEquals('direction@entreprise.com', $demande->email_destinataire);
        $this->assertStringContainsString('Dell Latitude 5540', $demande->objet_email);
        $this->assertStringContainsString('Dégât des eaux', $demande->corps_email, false);
        $response->assertRedirect(route('it.demandes.show', $demande));
    }

    public function test_validation_echec_si_produit_inactif_ou_quantite_invalide(): void
    {
        // Produit inactif
        $response = $this->actingAs($this->itUser)->post(route('it.demandes.store'), [
            'produit_id' => $this->produitInactif->id,
            'quantite' => 1,
            'priorite' => 'normale',
            'motif' => 'Test',
        ]);
        $response->assertSessionHasErrors('produit_id');

        // Quantité négative
        $response2 = $this->actingAs($this->itUser)->post(route('it.demandes.store'), [
            'produit_id' => $this->produitActif->id,
            'quantite' => 0,
            'priorite' => 'normale',
            'motif' => 'Test',
        ]);
        $response2->assertSessionHasErrors('quantite');

        // Motif vide
        $response3 = $this->actingAs($this->itUser)->post(route('it.demandes.store'), [
            'produit_id' => $this->produitActif->id,
            'quantite' => 1,
            'priorite' => 'normale',
            'motif' => '',
        ]);
        $response3->assertSessionHasErrors('motif');
    }

    public function test_enregistrement_ouverture_gmail_via_ajax(): void
    {
        $demande = DemandeMateriel::create([
            'reference' => DemandeMateriel::generateReference(),
            'produit_id' => $this->produitActif->id,
            'user_id' => $this->itUser->id,
            'quantite' => 1,
            'priorite' => 'normale',
            'motif' => 'Test motif',
            'statut_preparation' => DemandeMateriel::STATUT_PRETE,
            'email_destinataire' => 'direction@entreprise.com',
            'objet_email' => 'Demande test',
            'corps_email' => 'Bonjour',
        ]);

        $response = $this->actingAs($this->itUser)->postJson(route('it.demandes.gmail-opened', $demande));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(DemandeMateriel::STATUT_OUVERTURE_GMAIL, $demande->fresh()->statut_preparation);
        $this->assertNotNull($demande->fresh()->date_ouverture_gmail);
    }

    public function test_duplication_demande(): void
    {
        $demande = DemandeMateriel::create([
            'reference' => DemandeMateriel::generateReference(),
            'produit_id' => $this->produitActif->id,
            'user_id' => $this->itUser->id,
            'quantite' => 3,
            'priorite' => 'importante',
            'motif' => 'Demande initiale',
            'statut_preparation' => DemandeMateriel::STATUT_OUVERTURE_GMAIL,
            'email_destinataire' => 'direction@entreprise.com',
            'objet_email' => 'Demande test',
            'corps_email' => 'Bonjour',
        ]);

        $response = $this->actingAs($this->itUser)->post(route('it.demandes.duplicate', $demande));
        $response->assertSessionHas('success');

        $this->assertEquals(2, DemandeMateriel::count());
        $newDemande = DemandeMateriel::latest('id')->first();
        $this->assertNotEquals($demande->reference, $newDemande->reference);
        $this->assertEquals(DemandeMateriel::STATUT_BROUILLON, $newDemande->statut_preparation);
        $this->assertEquals(3, $newDemande->quantite);
    }

    public function test_archivage_demande(): void
    {
        $demande = DemandeMateriel::create([
            'reference' => DemandeMateriel::generateReference(),
            'produit_id' => $this->produitActif->id,
            'user_id' => $this->itUser->id,
            'quantite' => 1,
            'priorite' => 'normale',
            'motif' => 'Demande à archiver',
            'statut_preparation' => DemandeMateriel::STATUT_BROUILLON,
            'email_destinataire' => 'direction@entreprise.com',
            'objet_email' => 'Demande',
            'corps_email' => 'Bonjour',
        ]);

        $this->actingAs($this->itUser)->patch(route('it.demandes.archive', $demande));
        $this->assertTrue($demande->fresh()->est_archive);
    }

    public function test_admin_can_update_director_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('admin.settings.update'), [
            'directeur_nom' => 'M. Le Directeur Général',
            'directeur_email' => 'dg@societe.com',
            'nom_entreprise' => 'Nouvelle Entreprise SAS',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('dg@societe.com', AppSetting::get('directeur_email'));
        $this->assertEquals('M. Le Directeur Général', AppSetting::get('directeur_nom'));
    }

    public function test_service_demande_gmail_encode_correctement(): void
    {
        $service = app(DemandeGmailService::class);
        $url = $service->buildGmailUrl('dg@test.com', 'Demande: Matériel & Équipement', "Bonjour,\n\nTest accentué éàç");

        $this->assertStringStartsWith('https://mail.google.com/mail/?view=cm&fs=1', $url);
        $this->assertStringContainsString('dg%40test.com', $url);
        $this->assertStringContainsString('Mat%C3%A9riel', $url);
    }
}
