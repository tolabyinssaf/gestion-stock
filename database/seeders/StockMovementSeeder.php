<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use App\Services\StockMovementService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the stock and movements demo data seeds.
     */
    public function run(): void
    {
        $user = User::where('role', User::ROLE_RESPONSABLE_IT)->first() ?? User::first();
        if (!$user) return;

        // 1. Ensure Emplacements exist
        $magasinPrincipal = Emplacement::firstOrCreate(
            ['code' => 'MAG-01'],
            [
                'nom' => 'Magasin IT Principal',
                'site' => 'Siège Social - Bâtiment A, Étage 1',
                'description' => 'Stock central des matériels informatiques et pièces détachées.',
                'est_actif' => true,
            ]
        );

        $armoireServeur = Emplacement::firstOrCreate(
            ['code' => 'SRV-01'],
            [
                'nom' => 'Salle Serveurs / Baie IT',
                'site' => 'Siège Social - Sous-sol',
                'description' => 'Stockage des équipements réseau et serveurs de rechange.',
                'est_actif' => true,
            ]
        );

        $reserveBureautique = Emplacement::firstOrCreate(
            ['code' => 'RES-02'],
            [
                'nom' => 'Réserve Étage 2 (Open Space)',
                'site' => 'Siège Social - Bâtiment B, Étage 2',
                'description' => 'Petits consommables, câbles et claviers à disposition immédiate.',
                'est_actif' => true,
            ]
        );

        $bureauSupport = Emplacement::firstOrCreate(
            ['code' => 'SUP-01'],
            [
                'nom' => 'Atelier Support & Maintenance',
                'site' => 'Siège Social - Bâtiment A, RDC',
                'description' => 'Matériels en cours de préparation ou de diagnostic.',
                'est_actif' => true,
            ]
        );

        // 2. Identify products
        $souris = Produit::where('type_suivi', Produit::TYPE_QUANTITE)->first();
        $autreQuantite = Produit::where('type_suivi', Produit::TYPE_QUANTITE)->skip(1)->first();
        $laptopIndividuel = Equipement::first();

        // 3. Initialize stock lines and sample movements if none exist
        if (Mouvement::count() === 0 && $souris) {
            $service = app(StockMovementService::class);

            // Entrée de 20 souris au Magasin Principal
            $service->enregistrerEntree([
                'produit_id' => $souris->id,
                'emplacement_destination_id' => $magasinPrincipal->id,
                'quantite' => 25,
                'motif' => 'Approvisionnement initial commande Fournisseur IT-2026',
                'commentaire' => 'Lot de souris bureautiques neuves',
                'date_mouvement' => now()->subDays(5),
            ], $user);

            // Sortie de 3 souris pour le service RH
            $service->enregistrerSortie([
                'produit_id' => $souris->id,
                'emplacement_source_id' => $magasinPrincipal->id,
                'quantite' => 3,
                'beneficiaire' => 'Équipe Ressources Humaines',
                'motif' => 'Remplacement périphériques défectueux',
                'commentaire' => 'Validation ticket GLPI #1092',
                'date_mouvement' => now()->subDays(3),
            ], $user);

            // Transfert de 5 souris vers la Réserve Bureautique
            $service->enregistrerTransfert([
                'produit_id' => $souris->id,
                'emplacement_source_id' => $magasinPrincipal->id,
                'emplacement_destination_id' => $reserveBureautique->id,
                'quantite' => 5,
                'motif' => 'Dotation réserve de proximité',
                'commentaire' => 'Transfert hebdomadaire',
                'date_mouvement' => now()->subDays(1),
            ], $user);

            // Entrée / Stock pour un 2ème article si présent
            if ($autreQuantite) {
                $service->enregistrerEntree([
                    'produit_id' => $autreQuantite->id,
                    'emplacement_destination_id' => $magasinPrincipal->id,
                    'quantite' => 8,
                    'motif' => 'Stock initial',
                    'commentaire' => 'Réception accessoires',
                    'date_mouvement' => now()->subDays(4),
                ], $user);
            }
        }
    }
}
