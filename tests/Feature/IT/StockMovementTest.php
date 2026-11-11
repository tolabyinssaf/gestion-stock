<?php

namespace Tests\Feature\IT;

use App\Models\Category;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use App\Services\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $itUser;
    protected Emplacement $emplacementA;
    protected Emplacement $emplacementB;
    protected Produit $produitQuantite;
    protected Produit $produitIndividuel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itUser = User::factory()->create([
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
        ]);

        $category = Category::create([
            'nom' => 'Périphériques',
            'code' => 'PERIPH',
            'description' => 'Matériel bureautique',
            'est_actif' => true,
        ]);

        $this->emplacementA = Emplacement::create([
            'nom' => 'Magasin Central',
            'code' => 'MAG-CENTRAL',
            'site' => 'Siège',
            'est_actif' => true,
        ]);

        $this->emplacementB = Emplacement::create([
            'nom' => 'Atelier Support',
            'code' => 'ATELIER-SUP',
            'site' => 'Siège',
            'est_actif' => true,
        ]);

        $this->produitQuantite = Produit::create([
            'designation' => 'Souris Optique USB Logitech',
            'reference' => 'PER-MOU-001',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
            'seuil_minimum' => 5,
            'category_id' => $category->id,
        ]);

        $this->produitIndividuel = Produit::create([
            'designation' => 'Dell Latitude 5540',
            'reference' => 'PC-PORT-001',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
            'seuil_minimum' => 2,
            'category_id' => $category->id,
        ]);
    }

    public function test_responsable_it_can_access_stock_overview_page(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('it.stocks.index'));
        $response->assertStatus(200);
        $response->assertSee('État du stock');
        $response->assertSee('Souris Optique USB Logitech');
    }

    public function test_enregistrer_entree_stock_quantite_augmente_le_stock(): void
    {
        $service = app(StockMovementService::class);

        $mouvement = $service->enregistrerEntree([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_destination_id' => $this->emplacementA->id,
            'quantite' => 15,
            'motif' => 'Achat direct',
            'commentaire' => 'Lot de test',
            'date_mouvement' => now(),
        ], $this->itUser);

        $this->assertInstanceOf(Mouvement::class, $mouvement);
        $this->assertEquals(Mouvement::TYPE_ENTREE, $mouvement->type);
        $this->assertStringStartsWith('MVT-ENT-', $mouvement->reference);

        // Check stock table
        $stockLine = Stock::where('produit_id', $this->produitQuantite->id)
            ->where('emplacement_id', $this->emplacementA->id)
            ->first();

        $this->assertNotNull($stockLine);
        $this->assertEquals(15, $stockLine->quantite);
        $this->assertEquals(15, $this->produitQuantite->fresh()->total_stock);
    }

    public function test_enregistrer_sortie_stock_diminue_le_stock_et_bloque_si_insuffisant(): void
    {
        $service = app(StockMovementService::class);

        // Initial stock = 10
        $service->enregistrerEntree([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_destination_id' => $this->emplacementA->id,
            'quantite' => 10,
            'motif' => 'Initial',
            'date_mouvement' => now(),
        ], $this->itUser);

        // Valid sortie = 4
        $mouvement = $service->enregistrerSortie([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_source_id' => $this->emplacementA->id,
            'quantite' => 4,
            'beneficiaire' => 'Comptabilité',
            'motif' => 'Demande interne',
            'date_mouvement' => now(),
        ], $this->itUser);

        $this->assertEquals(Mouvement::TYPE_SORTIE, $mouvement->type);
        $this->assertStringStartsWith('MVT-SOR-', $mouvement->reference);
        $this->assertEquals(6, $this->produitQuantite->fresh()->total_stock);

        // Sortie impossible: 10 > 6 disponible
        $this->expectException(\InvalidArgumentException::class);
        $service->enregistrerSortie([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_source_id' => $this->emplacementA->id,
            'quantite' => 10,
            'beneficiaire' => 'RH',
            'motif' => 'Trop demandé',
            'date_mouvement' => now(),
        ], $this->itUser);
    }

    public function test_transfert_inter_emplacements_preserve_le_stock_global(): void
    {
        $service = app(StockMovementService::class);

        // Stock initial de 20 sur A
        $service->enregistrerEntree([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_destination_id' => $this->emplacementA->id,
            'quantite' => 20,
            'motif' => 'Réception',
            'date_mouvement' => now(),
        ], $this->itUser);

        // Transfert de 7 de A vers B
        $mouvement = $service->enregistrerTransfert([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_source_id' => $this->emplacementA->id,
            'emplacement_destination_id' => $this->emplacementB->id,
            'quantite' => 7,
            'motif' => 'Approvisionnement atelier',
            'date_mouvement' => now(),
        ], $this->itUser);

        $this->assertEquals(Mouvement::TYPE_TRANSFERT, $mouvement->type);
        $this->assertStringStartsWith('MVT-TRA-', $mouvement->reference);

        // Total stock invariant = 20
        $this->assertEquals(20, $this->produitQuantite->fresh()->total_stock);

        // Stock sur A = 13, Stock sur B = 7
        $this->assertEquals(13, Stock::where('produit_id', $this->produitQuantite->id)->where('emplacement_id', $this->emplacementA->id)->value('quantite'));
        $this->assertEquals(7, Stock::where('produit_id', $this->produitQuantite->id)->where('emplacement_id', $this->emplacementB->id)->value('quantite'));
    }

    public function test_correction_stock_ajuste_la_quantite_reelle(): void
    {
        $service = app(StockMovementService::class);

        // Initial stock = 10
        $service->enregistrerEntree([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_destination_id' => $this->emplacementA->id,
            'quantite' => 10,
            'motif' => 'Initial',
            'date_mouvement' => now(),
        ], $this->itUser);

        // Régularisation d'inventaire: recomptage physique = 8 (écart de -2)
        $mouvement = $service->enregistrerCorrection([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_id' => $this->emplacementA->id,
            'nouvelle_quantite' => 8,
            'motif' => 'Inventaire annuel - casse constatée',
            'date_mouvement' => now(),
        ], $this->itUser);

        $this->assertEquals(Mouvement::TYPE_CORRECTION, $mouvement->type);
        $this->assertStringStartsWith('MVT-COR-', $mouvement->reference);
        $this->assertEquals(8, $this->produitQuantite->fresh()->total_stock);
    }

    public function test_emplacement_occupe_ne_peut_pas_etre_supprime(): void
    {
        $service = app(StockMovementService::class);

        // Mettre du stock sur l'emplacement A
        $service->enregistrerEntree([
            'produit_id' => $this->produitQuantite->id,
            'emplacement_destination_id' => $this->emplacementA->id,
            'quantite' => 5,
            'motif' => 'Initial',
            'date_mouvement' => now(),
        ], $this->itUser);

        // Tentative de suppression via le contrôleur
        $response = $this->actingAs($this->itUser)->delete(route('it.emplacements.destroy', $this->emplacementA));
        $response->assertRedirect(route('it.emplacements.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('emplacements', ['id' => $this->emplacementA->id]);
    }
}
