<?php

namespace Tests\Feature\IT;

use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\EquipementActivite;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipementTest extends TestCase
{
    use RefreshDatabase;

    protected User $itUser;
    protected User $adminUser;
    protected Categorie $categorie;
    protected Produit $produitIndividuel;
    protected Produit $produitQuantite;
    protected Emplacement $emplacement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itUser = User::factory()->responsableIt()->create();
        $this->adminUser = User::factory()->admin()->create();

        $this->categorie = Categorie::create([
            'nom' => 'Ordinateurs portables',
            'slug' => 'ordinateurs-portables',
            'icon_name' => 'laptop',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $this->produitIndividuel = Produit::create([
            'categorie_id' => $this->categorie->id,
            'nom' => 'Dell Latitude 5440',
            'marque' => 'Dell',
            'modele' => '5440',
            'reference' => 'PC-DEL-5440',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $this->produitQuantite = Produit::create([
            'categorie_id' => $this->categorie->id,
            'nom' => 'Souris USB Filaire',
            'marque' => 'Logitech',
            'modele' => 'B100',
            'reference' => 'ACC-LOG-B100',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $this->emplacement = Emplacement::create([
            'nom' => 'Réserve IT',
            'code' => 'LOC-RES-IT',
            'statut' => Emplacement::STATUT_ACTIF,
        ]);
    }

    // -------------------------------------------------------------
    // 1. Accès & Protection des routes
    // -------------------------------------------------------------

    public function test_guest_is_redirected_to_login_when_accessing_equipements(): void
    {
        $response = $this->get(route('it.equipements.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_it_equipements(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('it.equipements.index'));
        $response->assertStatus(403);
    }

    // -------------------------------------------------------------
    // 2. Affichage & Consultation
    // -------------------------------------------------------------

    public function test_responsable_it_can_view_equipements_list(): void
    {
        Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-2026-00001',
            'numero_serie' => 'SN-DEL-12345',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
            'emplacement_id' => $this->emplacement->id,
            'statut' => Equipement::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->get(route('it.equipements.index'));

        $response->assertStatus(200);
        $response->assertSee('Équipements informatiques');
        $response->assertSee('EQ-2026-00001');
        $response->assertSee('SN-DEL-12345');
        $response->assertSee('Dell Latitude 5440');
    }

    public function test_responsable_it_can_render_equipement_create_page(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('it.equipements.create'));

        $response->assertStatus(200);
        $response->assertSee('Enregistrer un équipement physique');
        $response->assertSee('Dell Latitude 5440');
        $response->assertDontSee('ACC-LOG-B100'); // Produit en quantité exclus du dropdown
    }

    // -------------------------------------------------------------
    // 3. Création & Validations métier
    // -------------------------------------------------------------

    public function test_responsable_it_can_create_an_equipment_with_audit_log(): void
    {
        $response = $this->actingAs($this->itUser)->post(route('it.equipements.store'), [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-2026-00100',
            'numero_serie' => 'ABC987XYZ',
            'date_entree' => '2026-01-15',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
            'emplacement_id' => $this->emplacement->id,
            'observations' => 'Matériel neuf sous emballage d\'origine.',
            'statut' => Equipement::STATUT_ACTIF,
        ]);

        $this->assertDatabaseHas('equipements', [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-2026-00100',
            'numero_serie' => 'ABC987XYZ',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
            'emplacement_id' => $this->emplacement->id,
            'created_by' => $this->itUser->id,
        ]);

        $equipement = Equipement::where('code_inventaire', 'EQ-2026-00100')->first();
        $this->assertNotNull($equipement);

        // Audit log created
        $this->assertDatabaseHas('equipement_activites', [
            'equipement_id' => $equipement->id,
            'user_id' => $this->itUser->id,
            'action' => 'creation',
        ]);

        $response->assertRedirect(route('it.equipements.show', $equipement));
    }

    public function test_cannot_create_equipment_for_quantity_product(): void
    {
        $response = $this->actingAs($this->itUser)->post(route('it.equipements.store'), [
            'produit_id' => $this->produitQuantite->id, // Type quantité
            'code_inventaire' => 'EQ-2026-00101',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response->assertSessionHasErrors('produit_id');
        $this->assertDatabaseMissing('equipements', ['code_inventaire' => 'EQ-2026-00101']);
    }

    public function test_inventory_code_must_be_unique(): void
    {
        Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-UNIQUE-01',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response = $this->actingAs($this->itUser)->post(route('it.equipements.store'), [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-UNIQUE-01',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response->assertSessionHasErrors('code_inventaire');
    }

    public function test_serial_number_must_be_unique(): void
    {
        Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-SN-01',
            'numero_serie' => 'SN-DOUBLE-CHECK',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response = $this->actingAs($this->itUser)->post(route('it.equipements.store'), [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-SN-02',
            'numero_serie' => 'SN-DOUBLE-CHECK',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response->assertSessionHasErrors('numero_serie');
    }

    public function test_incoherent_status_and_condition_is_rejected(): void
    {
        // Hors service cannot be disponible
        $response = $this->actingAs($this->itUser)->post(route('it.equipements.store'), [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-INCOHERENT-01',
            'etat_physique' => Equipement::ETAT_HORS_SERVICE,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response->assertSessionHasErrors('statut_utilisation');

        // En panne cannot be disponible
        $response2 = $this->actingAs($this->itUser)->post(route('it.equipements.store'), [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-INCOHERENT-02',
            'etat_physique' => Equipement::ETAT_EN_PANNE,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        $response2->assertSessionHasErrors('statut_utilisation');
    }

    // -------------------------------------------------------------
    // 4. Consultation & Modification
    // -------------------------------------------------------------

    public function test_responsable_it_can_view_equipment_details(): void
    {
        $equipement = Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-DETAIL-01',
            'numero_serie' => 'SN-DETAIL-999',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_UTILISATION,
            'emplacement_id' => $this->emplacement->id,
            'observations' => 'Poste assigné à la direction.',
            'created_by' => $this->itUser->id,
        ]);

        EquipementActivite::create([
            'equipement_id' => $equipement->id,
            'user_id' => $this->itUser->id,
            'action' => 'creation',
            'description' => 'Création initiale du matériel.',
        ]);

        $response = $this->actingAs($this->itUser)->get(route('it.equipements.show', $equipement));

        $response->assertStatus(200);
        $response->assertSee('EQ-DETAIL-01');
        $response->assertSee('SN-DETAIL-999');
        $response->assertSee('Dell Latitude 5440');
        $response->assertSee('Réserve IT');
        $response->assertSee('Journal des modifications & Traçabilité');
    }

    public function test_responsable_it_can_update_equipment_and_tracks_audit(): void
    {
        $equipement = Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-UPDATE-01',
            'numero_serie' => 'SN-OLD-VAL',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
            'emplacement_id' => $this->emplacement->id,
        ]);

        $nouveauBureau = Emplacement::create([
            'nom' => 'Bureau direction',
            'code' => 'LOC-DIR-01',
            'statut' => Emplacement::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->put(route('it.equipements.update', $equipement), [
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-UPDATE-01',
            'numero_serie' => 'SN-NEW-VAL',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_UTILISATION,
            'emplacement_id' => $nouveauBureau->id,
            'observations' => 'Déployé au bureau de direction.',
        ]);

        $response->assertRedirect(route('it.equipements.show', $equipement));

        $this->assertDatabaseHas('equipements', [
            'id' => $equipement->id,
            'numero_serie' => 'SN-NEW-VAL',
            'statut_utilisation' => Equipement::STATUT_UTILISATION,
            'emplacement_id' => $nouveauBureau->id,
            'updated_by' => $this->itUser->id,
        ]);

        // Audit log created with old/new values
        $this->assertDatabaseHas('equipement_activites', [
            'equipement_id' => $equipement->id,
            'user_id' => $this->itUser->id,
            'action' => 'modification',
        ]);
    }

    // -------------------------------------------------------------
    // 5. Archivage & Réactivation
    // -------------------------------------------------------------

    public function test_responsable_it_can_toggle_equipment_archive_status(): void
    {
        $equipement = Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-ARCHIVE-01',
            'etat_physique' => Equipement::ETAT_HORS_SERVICE,
            'statut_utilisation' => Equipement::STATUT_RETIRE,
            'statut' => Equipement::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->patch(route('it.equipements.toggle-status', $equipement));
        $response->assertRedirect();
        $this->assertEquals(Equipement::STATUT_ARCHIVE, $equipement->fresh()->statut);

        // Reactivate
        $response2 = $this->actingAs($this->itUser)->patch(route('it.equipements.toggle-status', $equipement));
        $this->assertEquals(Equipement::STATUT_ACTIF, $equipement->fresh()->statut);
    }

    // -------------------------------------------------------------
    // 6. Suppression sécurisée
    // -------------------------------------------------------------

    public function test_cannot_hard_delete_equipment_with_audit_history(): void
    {
        $equipement = Equipement::create([
            'produit_id' => $this->produitIndividuel->id,
            'code_inventaire' => 'EQ-HIST-01',
            'etat_physique' => Equipement::ETAT_BON,
            'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
        ]);

        // Simulate 2 activities (creation + modification)
        EquipementActivite::create([
            'equipement_id' => $equipement->id,
            'action' => 'creation',
            'description' => 'Création',
        ]);
        EquipementActivite::create([
            'equipement_id' => $equipement->id,
            'action' => 'modification',
            'description' => 'Modification',
        ]);

        $response = $this->actingAs($this->itUser)->delete(route('it.equipements.destroy', $equipement));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('equipements', ['id' => $equipement->id]);
    }
}
