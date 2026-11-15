<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use App\Services\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SituationStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $gestionnaire;
    protected Categorie $categorie;
    protected Emplacement $reserve;
    protected Emplacement $production;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestionnaire = User::factory()->create([
            'role' => User::ROLE_RESPONSABLE_IT,
            'name' => 'Gestionnaire Test',
        ]);

        $this->categorie = Categorie::create([
            'nom' => 'Écrans & Moniteurs',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $this->reserve = Emplacement::create([
            'nom' => 'Réserve IT',
            'code' => 'LOC-RES-IT',
            'statut' => Emplacement::STATUT_ACTIF,
        ]);

        $this->production = Emplacement::create([
            'nom' => 'Salle de production',
            'code' => 'LOC-PROD-01',
            'statut' => Emplacement::STATUT_ACTIF,
        ]);
    }

    /**
     * Test complet du scénario imposé par la spécification :
     * Produit : Écran Dell 24 pouces
     * 1. Réception initiale de 20 unités en stock (réserve).
     * 2. Mise en utilisation de 12 unités en salle de production.
     * 3. Déclaration de panne de 3 unités parmi celles en utilisation.
     * 4. Retour de 2 unités en utilisation vers la réserve.
     * 5. Vérification des totaux : 10 en stock, 7 en utilisation, 3 en panne. Total absolu = 20.
     */
    public function test_scenario_complet_situations_produit_dell(): void
    {
        $service = app(StockMovementService::class);

        // Création du produit avec seuil minimum de 5
        $produit = Produit::create([
            'nom' => 'Écran Dell 24 pouces',
            'categorie_id' => $this->categorie->id,
            'marque' => 'Dell',
            'reference' => 'DELL-P2422H',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'seuil_minimum' => 5,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        // Étape 1 : Réception de 20 unités en stock
        $service->enregistrerEntree([
            'produit_id' => $produit->id,
            'emplacement_id' => $this->reserve->id,
            'quantite' => 20,
            'situation' => Stock::SITUATION_EN_STOCK,
            'motif' => 'Réception commande initiale',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(20, $produit->quantite_en_stock);
        $this->assertEquals(0, $produit->quantite_en_utilisation);
        $this->assertEquals(0, $produit->quantite_en_panne);
        $this->assertEquals(20, $produit->total_stock);
        $this->assertEquals('normal', $produit->stock_status); // 20 > 5 => Disponible

        // Étape 2 : Mise en utilisation de 12 unités
        $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_STOCK,
            'situation_arrivee' => Stock::SITUATION_EN_UTILISATION,
            'quantite' => 12,
            'emplacement_source_id' => $this->reserve->id,
            'emplacement_destination_id' => $this->production->id,
            'motif' => 'Déploiement sur postes de production',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(8, $produit->quantite_en_stock);
        $this->assertEquals(12, $produit->quantite_en_utilisation);
        $this->assertEquals(0, $produit->quantite_en_panne);
        $this->assertEquals(20, $produit->total_stock);
        $this->assertEquals('normal', $produit->stock_status); // 8 > 5 => Disponible

        // Étape 3 : Déclaration de panne de 3 unités en utilisation
        $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_UTILISATION,
            'situation_arrivee' => Stock::SITUATION_EN_PANNE,
            'quantite' => 3,
            'emplacement_source_id' => $this->production->id,
            'emplacement_destination_id' => $this->reserve->id,
            'motif' => 'Écrans défaillants signalés',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(8, $produit->quantite_en_stock);
        $this->assertEquals(9, $produit->quantite_en_utilisation);
        $this->assertEquals(3, $produit->quantite_en_panne);
        $this->assertEquals(20, $produit->total_stock);

        // Étape 4 : Retour de 2 unités en utilisation vers le stock
        $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_UTILISATION,
            'situation_arrivee' => Stock::SITUATION_EN_STOCK,
            'quantite' => 2,
            'emplacement_source_id' => $this->production->id,
            'emplacement_destination_id' => $this->reserve->id,
            'motif' => 'Retour matériel non utilisé',
        ], $this->gestionnaire);

        $produit->refresh();
        // Étape 5 : Bilan final exact
        $this->assertEquals(10, $produit->quantite_en_stock);
        $this->assertEquals(7, $produit->quantite_en_utilisation);
        $this->assertEquals(3, $produit->quantite_en_panne);
        $this->assertEquals(20, $produit->total_stock);
        $this->assertEquals(20, $produit->quantite_totale);
    }

    /**
     * Vérification de l'alerte stock faible selon seuil_minimum personnalisé.
     */
    public function test_alerte_stock_faible_selon_seuil_minimum(): void
    {
        $service = app(StockMovementService::class);

        $souris = Produit::create([
            'nom' => 'Souris Logitech MX Master',
            'categorie_id' => $this->categorie->id,
            'seuil_minimum' => 5,
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        // Entrée initiale de 3 unités (3 <= 5)
        $service->enregistrerEntree([
            'produit_id' => $souris->id,
            'emplacement_id' => $this->reserve->id,
            'quantite' => 3,
            'situation' => Stock::SITUATION_EN_STOCK,
            'motif' => 'Stock initial',
        ], $this->gestionnaire);

        $souris->refresh();
        $this->assertEquals('faible', $souris->stock_status);
        $this->assertEquals('Stock faible', $souris->stock_status_label);

        // Sortie définitive de 3 unités (devient 0)
        $service->enregistrerSortie([
            'produit_id' => $souris->id,
            'emplacement_id' => $this->reserve->id,
            'quantite' => 3,
            'situation' => Stock::SITUATION_EN_STOCK,
            'motif' => 'Mise au rebut',
        ], $this->gestionnaire);

        $souris->refresh();
        $this->assertEquals('rupture', $souris->stock_status);
        $this->assertEquals('Épuisé', $souris->stock_status_label);
    }

    /**
     * Vérification de l'impossibilité de déplacer plus que le stock disponible.
     */
    public function test_rejet_changement_situation_si_quantite_insuffisante(): void
    {
        $this->expectException(ValidationException::class);

        $service = app(StockMovementService::class);

        $produit = Produit::create([
            'nom' => 'Clavier Standard',
            'categorie_id' => $this->categorie->id,
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $service->enregistrerEntree([
            'produit_id' => $produit->id,
            'emplacement_id' => $this->reserve->id,
            'quantite' => 2,
            'situation' => Stock::SITUATION_EN_STOCK,
            'motif' => 'Stock initial',
        ], $this->gestionnaire);

        // Tenter d'en mettre 5 en utilisation alors qu'il n'y en a que 2
        $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_STOCK,
            'situation_arrivee' => Stock::SITUATION_EN_UTILISATION,
            'quantite' => 5,
            'emplacement_source_id' => $this->reserve->id,
            'emplacement_destination_id' => $this->production->id,
            'motif' => 'Test excédent',
        ], $this->gestionnaire);
    }

    /**
     * Vérification de l'obligation de confirmer que le matériel est fonctionnel
     * avant de le remettre en stock ou en utilisation depuis la situation 'en_panne'.
     */
    public function test_confirmation_obligatoire_pour_remise_en_service_depuis_panne(): void
    {
        $service = app(StockMovementService::class);

        $produit = Produit::create([
            'nom' => 'Écran de test',
            'categorie_id' => $this->categorie->id,
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        // Créer directement 1 unité en panne
        Stock::create([
            'produit_id' => $produit->id,
            'emplacement_id' => $this->reserve->id,
            'situation' => Stock::SITUATION_EN_PANNE,
            'quantite' => 1,
        ]);

        // Sans confirmation => Doit lever une exception
        try {
            $service->changerSituation([
                'produit_id' => $produit->id,
                'situation_depart' => Stock::SITUATION_EN_PANNE,
                'situation_arrivee' => Stock::SITUATION_EN_STOCK,
                'quantite' => 1,
                'emplacement_source_id' => $this->reserve->id,
                'emplacement_destination_id' => $this->reserve->id,
                'motif' => 'Test sans confirmation',
                'confirme_fonctionnel' => false,
            ], $this->gestionnaire);

            $this->fail('Une ValidationException aurait dû être levée car le matériel n\'a pas été confirmé fonctionnel.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('confirme_fonctionnel', $e->errors());
        }

        // Avec confirmation => Doit réussir
        $mvt = $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_PANNE,
            'situation_arrivee' => Stock::SITUATION_EN_STOCK,
            'quantite' => 1,
            'emplacement_source_id' => $this->reserve->id,
            'emplacement_destination_id' => $this->reserve->id,
            'motif' => 'Réparation effectuée avec succès',
            'confirme_fonctionnel' => true,
        ], $this->gestionnaire);

        $this->assertEquals(Mouvement::TYPE_REMISE_SERVICE, $mvt->type);
        $produit->refresh();
        $this->assertEquals(1, $produit->quantite_en_stock);
        $this->assertEquals(0, $produit->quantite_en_panne);
    }

    /**
     * Vérification de la gestion simplifiée sans aucun emplacement :
     * 1. Entrée directe sans emplacement_id (utilise l'emplacement technique par défaut).
     * 2. Changement de situation sans emplacements source ni destination.
     * 3. Sortie définitive sans emplacement.
     */
    public function test_gestion_stock_simplifiee_sans_emplacements(): void
    {
        $service = app(StockMovementService::class);

        $produit = Produit::create([
            'nom' => 'Unité Centrale Lenovo ThinkCentre',
            'categorie_id' => $this->categorie->id,
            'reference' => 'LENOVO-TC-001',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'seuil_minimum' => 2,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        // 1. Entrée sans aucun emplacement
        $service->enregistrerEntree([
            'produit_id' => $produit->id,
            'quantite' => 10,
            'situation' => Stock::SITUATION_EN_STOCK,
            'motif' => 'Arrivage direct sans emplacement',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(10, $produit->quantite_en_stock);
        $this->assertEquals(0, $produit->quantite_en_utilisation);
        $this->assertEquals(0, $produit->quantite_en_panne);
        $this->assertEquals(10, $produit->total_stock);

        // 2. Déplacement En stock -> En utilisation sans emplacements
        $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_STOCK,
            'situation_arrivee' => Stock::SITUATION_EN_UTILISATION,
            'quantite' => 6,
            'motif' => 'Distribution aux agents',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(4, $produit->quantite_en_stock);
        $this->assertEquals(6, $produit->quantite_en_utilisation);
        $this->assertEquals(0, $produit->quantite_en_panne);
        $this->assertEquals(10, $produit->total_stock);

        // 3. Déclaration En utilisation -> En panne sans emplacements
        $service->changerSituation([
            'produit_id' => $produit->id,
            'situation_depart' => Stock::SITUATION_EN_UTILISATION,
            'situation_arrivee' => Stock::SITUATION_EN_PANNE,
            'quantite' => 2,
            'motif' => 'Panne carte mère',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(4, $produit->quantite_en_stock);
        $this->assertEquals(4, $produit->quantite_en_utilisation);
        $this->assertEquals(2, $produit->quantite_en_panne);
        $this->assertEquals(10, $produit->total_stock);

        // 4. Sortie définitive depuis En panne sans emplacement
        $service->enregistrerSortie([
            'produit_id' => $produit->id,
            'quantite' => 1,
            'situation' => Stock::SITUATION_EN_PANNE,
            'motif' => 'Mise au rebut irréparable',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(4, $produit->quantite_en_stock);
        $this->assertEquals(4, $produit->quantite_en_utilisation);
        $this->assertEquals(1, $produit->quantite_en_panne);
        $this->assertEquals(9, $produit->total_stock);

        // 5. Correction physique de la situation en_stock sans emplacement
        $service->enregistrerCorrection([
            'produit_id' => $produit->id,
            'situation' => Stock::SITUATION_EN_STOCK,
            'nouvelle_quantite' => 5,
            'motif' => 'Ajustement inventaire physique',
        ], $this->gestionnaire);

        $produit->refresh();
        $this->assertEquals(5, $produit->quantite_en_stock);
        $this->assertEquals(4, $produit->quantite_en_utilisation);
        $this->assertEquals(1, $produit->quantite_en_panne);
        $this->assertEquals(10, $produit->total_stock);
    }
}
