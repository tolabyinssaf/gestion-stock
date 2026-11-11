<?php

namespace App\Services;

use App\Models\Equipement;
use App\Models\EquipementActivite;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    protected ActivityLoggerService $activityLogger;

    public function __construct(ActivityLoggerService $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    /**
     * Enregistrer une entrée en stock.
     */
    public function enregistrerEntree(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $produit = Produit::findOrFail($data['produit_id']);
            $quantite = (int) ($data['quantite'] ?? 1);
            $destinationId = $data['emplacement_id'];
            $equipementId = $data['equipement_id'] ?? null;
            $equipement = null;

            if ($produit->isQuantite() || empty($equipementId)) {
                if ($quantite <= 0) {
                    throw ValidationException::withMessages([
                        'quantite' => 'La quantité reçue doit être strictement supérieure à zéro.',
                    ]);
                }

                // Lock and increment stock
                $stock = Stock::firstOrCreate(
                    [
                        'produit_id' => $produit->id,
                        'emplacement_id' => $destinationId,
                    ],
                    ['quantite' => 0]
                );

                $stock = Stock::where('id', $stock->id)->lockForUpdate()->first();
                $stock->quantite += $quantite;
                $stock->save();
            } else {
                // Individual equipment entry
                if (!$equipementId) {
                    throw ValidationException::withMessages([
                        'equipement_id' => 'Veuillez sélectionner l\'équipement individuel à réceptionner.',
                    ]);
                }

                $equipement = Equipement::where('id', $equipementId)->lockForUpdate()->firstOrFail();
                $ancienEmplacement = $equipement->emplacement->nom ?? 'Non assigné';

                $equipement->update([
                    'emplacement_id' => $destinationId,
                    'statut_utilisation' => Equipement::STATUT_DISPONIBLE,
                    'etat_physique' => $data['etat_physique'] ?? $equipement->etat_physique,
                ]);

                EquipementActivite::create([
                    'equipement_id' => $equipement->id,
                    'user_id' => $user->id,
                    'action' => 'modification',
                    'description' => "Entrée en stock / réintégration sur l'emplacement.",
                    'anciennes_valeurs' => ['emplacement' => $ancienEmplacement, 'statut' => $equipement->getOriginal('statut_utilisation')],
                    'nouvelles_valeurs' => ['emplacement' => $equipement->emplacement->nom ?? 'Non assigné', 'statut' => Equipement::STATUT_DISPONIBLE],
                ]);

                $quantite = 1;
            }

            $mvt = Mouvement::create([
                'reference' => Mouvement::generateReference(),
                'type' => Mouvement::TYPE_ENTREE,
                'produit_id' => $produit->id,
                'equipement_id' => $equipement?->id,
                'quantite' => $quantite,
                'emplacement_source_id' => null,
                'emplacement_destination_id' => $destinationId,
                'motif' => $data['motif'],
                'beneficiaire' => $data['beneficiaire'] ?? null,
                'commentaire' => $data['commentaire'] ?? null,
                'user_id' => $user->id,
                'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            ]);

            $this->activityLogger->log(
                module: 'stock',
                actionType: 'entree',
                description: "Entrée de stock : {$quantite} x {$produit->designation} (Réf {$mvt->reference})",
                entityType: 'Mouvement',
                entityId: $mvt->id,
                entityName: $mvt->reference,
                oldValues: null,
                newValues: ['type' => 'entree', 'quantite' => $quantite, 'produit' => $produit->designation, 'destination_id' => $destinationId],
                user: $user
            );

            return $mvt;
        });
    }

    /**
     * Enregistrer une sortie de stock.
     */
    public function enregistrerSortie(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $produit = Produit::findOrFail($data['produit_id']);
            $quantite = (int) ($data['quantite'] ?? 1);
            $sourceId = $data['emplacement_id'] ?? null;
            $equipementId = $data['equipement_id'] ?? null;
            $equipement = null;

            if ($produit->isQuantite() || empty($equipementId)) {
                if ($quantite <= 0) {
                    throw ValidationException::withMessages([
                        'quantite' => 'La quantité à sortir doit être strictement positive.',
                    ]);
                }

                $stock = Stock::where('produit_id', $produit->id)
                    ->where('emplacement_id', $sourceId)
                    ->lockForUpdate()
                    ->first();

                $disponible = $stock ? $stock->quantite : 0;

                if ($disponible < $quantite) {
                    throw ValidationException::withMessages([
                        'quantite' => "Stock insuffisant : vous demandez {$quantite} unité(s), mais seulement {$disponible} unité(s) sont disponibles sur cet emplacement.",
                    ]);
                }

                $stock->quantite -= $quantite;
                $stock->save();
            } else {
                if (!$equipementId) {
                    throw ValidationException::withMessages([
                        'equipement_id' => 'Veuillez sélectionner l\'équipement individuel concerné.',
                    ]);
                }

                $equipement = Equipement::where('id', $equipementId)->lockForUpdate()->firstOrFail();
                $sourceId = $equipement->emplacement_id;

                $nouveauStatutUtil = $data['nouveau_statut'] ?? Equipement::STATUT_UTILISATION;

                $equipement->update([
                    'statut_utilisation' => $nouveauStatutUtil,
                ]);

                EquipementActivite::create([
                    'equipement_id' => $equipement->id,
                    'user_id' => $user->id,
                    'action' => 'modification',
                    'description' => "Sortie de stock : {$data['motif']} (Bénéficiaire : " . ($data['beneficiaire'] ?? 'Non spécifié') . ").",
                    'anciennes_valeurs' => ['statut_utilisation' => $equipement->getOriginal('statut_utilisation')],
                    'nouvelles_valeurs' => ['statut_utilisation' => $nouveauStatutUtil],
                ]);

                $quantite = 1;
            }

            $mvt = Mouvement::create([
                'reference' => Mouvement::generateReference(),
                'type' => Mouvement::TYPE_SORTIE,
                'produit_id' => $produit->id,
                'equipement_id' => $equipement?->id,
                'quantite' => $quantite,
                'emplacement_source_id' => $sourceId,
                'emplacement_destination_id' => null,
                'motif' => $data['motif'],
                'beneficiaire' => $data['beneficiaire'] ?? null,
                'commentaire' => $data['commentaire'] ?? null,
                'user_id' => $user->id,
                'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            ]);

            $this->activityLogger->log(
                module: 'stock',
                actionType: 'sortie',
                description: "Sortie de stock : {$quantite} x {$produit->nom} (Réf {$mvt->reference}) - Motif: {$data['motif']}",
                entityType: 'Mouvement',
                entityId: $mvt->id,
                entityName: $mvt->reference,
                oldValues: null,
                newValues: ['type' => 'sortie', 'quantite' => $quantite, 'produit' => $produit->nom, 'source_id' => $sourceId, 'beneficiaire' => $data['beneficiaire'] ?? null],
                user: $user
            );

            return $mvt;
        });
    }

    /**
     * Transférer du matériel entre deux emplacements distincts.
     */
    public function enregistrerTransfert(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $sourceId = $data['emplacement_source_id'];
            $destId = $data['emplacement_destination_id'];

            if ($sourceId == $destId) {
                throw ValidationException::withMessages([
                    'emplacement_destination_id' => 'L\'emplacement de destination doit être différent de l\'emplacement d\'origine.',
                ]);
            }

            $produit = Produit::findOrFail($data['produit_id']);
            $quantite = (int) ($data['quantite'] ?? 1);
            $equipementId = $data['equipement_id'] ?? null;
            $equipement = null;

            if ($produit->isQuantite()) {
                if ($quantite <= 0) {
                    throw ValidationException::withMessages([
                        'quantite' => 'La quantité à transférer doit être strictement positive.',
                    ]);
                }

                $sourceStock = Stock::where('produit_id', $produit->id)
                    ->where('emplacement_id', $sourceId)
                    ->lockForUpdate()
                    ->first();

                $disponible = $sourceStock ? $sourceStock->quantite : 0;

                if ($disponible < $quantite) {
                    throw ValidationException::withMessages([
                        'quantite' => "Stock d'origine insuffisant : seulement {$disponible} unité(s) disponibles à transférer.",
                    ]);
                }

                // Decrement source
                $sourceStock->quantite -= $quantite;
                $sourceStock->save();

                // Increment destination
                $destStock = Stock::firstOrCreate(
                    [
                        'produit_id' => $produit->id,
                        'emplacement_id' => $destId,
                    ],
                    ['quantite' => 0]
                );

                $destStock = Stock::where('id', $destStock->id)->lockForUpdate()->first();
                $destStock->quantite += $quantite;
                $destStock->save();
            } else {
                if (!$equipementId) {
                    throw ValidationException::withMessages([
                        'equipement_id' => 'Veuillez sélectionner l\'équipement à transférer.',
                    ]);
                }

                $equipement = Equipement::where('id', $equipementId)->lockForUpdate()->firstOrFail();

                if ($equipement->emplacement_id == $destId) {
                    throw ValidationException::withMessages([
                        'emplacement_destination_id' => "L'équipement est déjà localisé sur cet emplacement.",
                    ]);
                }

                $ancienNom = $equipement->emplacement->nom ?? 'Non assigné';

                $equipement->update([
                    'emplacement_id' => $destId,
                ]);

                EquipementActivite::create([
                    'equipement_id' => $equipement->id,
                    'user_id' => $user->id,
                    'action' => 'modification',
                    'description' => "Transfert d'emplacement vers {$equipement->fresh()->emplacement->nom}.",
                    'anciennes_valeurs' => ['emplacement' => $ancienNom],
                    'nouvelles_valeurs' => ['emplacement' => $equipement->fresh()->emplacement->nom],
                ]);

                $quantite = 1;
            }

            $mvt = Mouvement::create([
                'reference' => Mouvement::generateReference(),
                'type' => Mouvement::TYPE_TRANSFERT,
                'produit_id' => $produit->id,
                'equipement_id' => $equipement?->id,
                'quantite' => $quantite,
                'emplacement_source_id' => $sourceId,
                'emplacement_destination_id' => $destId,
                'motif' => $data['motif'],
                'beneficiaire' => $data['beneficiaire'] ?? null,
                'commentaire' => $data['commentaire'] ?? null,
                'user_id' => $user->id,
                'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            ]);

            $this->activityLogger->log(
                module: 'stock',
                actionType: 'transfert',
                description: "Transfert de stock : {$quantite} x {$produit->nom} (Réf {$mvt->reference})",
                entityType: 'Mouvement',
                entityId: $mvt->id,
                entityName: $mvt->reference,
                oldValues: ['source_id' => $sourceId],
                newValues: ['destination_id' => $destId, 'quantite' => $quantite],
                user: $user
            );

            return $mvt;
        });
    }

    /**
     * Enregistrer une correction d'inventaire contrôlée (Produits par quantité).
     */
    public function enregistrerCorrection(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $produit = Produit::findOrFail($data['produit_id']);

            if (!$produit->isQuantite()) {
                throw ValidationException::withMessages([
                    'produit_id' => 'La correction de quantité par inventaire ne s\'applique qu\'aux produits suivis en volume.',
                ]);
            }

            $emplacementId = $data['emplacement_id'];
            $nouvelleQuantite = (int) $data['nouvelle_quantite'];

            if ($nouvelleQuantite < 0) {
                throw ValidationException::withMessages([
                    'nouvelle_quantite' => 'La quantité réelle d\'inventaire ne peut pas être négative.',
                ]);
            }

            $stock = Stock::firstOrCreate(
                [
                    'produit_id' => $produit->id,
                    'emplacement_id' => $emplacementId,
                ],
                ['quantite' => 0]
            );

            $stock = Stock::where('id', $stock->id)->lockForUpdate()->first();
            $ancienneQuantite = $stock->quantite;
            $ecart = $nouvelleQuantite - $ancienneQuantite;

            $stock->quantite = $nouvelleQuantite;
            $stock->save();

            $signe = $ecart >= 0 ? "+{$ecart}" : (string) $ecart;
            $commentaireComplet = "Inventaire physique : ancienne valeur {$ancienneQuantite}, nouvelle valeur {$nouvelleQuantite} (Écart : {$signe}).\n" . ($data['commentaire'] ?? '');

            $mvt = Mouvement::create([
                'reference' => Mouvement::generateReference(),
                'type' => Mouvement::TYPE_CORRECTION,
                'produit_id' => $produit->id,
                'equipement_id' => null,
                'quantite' => abs($ecart),
                'emplacement_source_id' => $ecart < 0 ? $emplacementId : null,
                'emplacement_destination_id' => $ecart > 0 ? $emplacementId : null,
                'motif' => $data['motif'],
                'commentaire' => $commentaireComplet,
                'user_id' => $user->id,
                'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            ]);

            $this->activityLogger->log(
                module: 'stock',
                actionType: 'correction',
                description: "Correction inventaire : {$produit->nom} à emplacement #{$emplacementId} (Ancienne: {$ancienneQuantite}, Nouvelle: {$nouvelleQuantite}, Écart: {$signe})",
                entityType: 'Mouvement',
                entityId: $mvt->id,
                entityName: $mvt->reference,
                oldValues: ['quantite' => $ancienneQuantite],
                newValues: ['quantite' => $nouvelleQuantite, 'ecart' => $ecart],
                user: $user
            );

            return $mvt;
        });
    }
}
