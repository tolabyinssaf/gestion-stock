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
     * Obtenir l'emplacement technique par défaut pour préserver l'intégrité de la BDD.
     */
    protected function getDefaultEmplacementId(): int
    {
        return \App\Models\Emplacement::firstOrCreate(
            ['code' => 'LOC-STOCK'],
            ['nom' => 'Stock général', 'statut' => 'actif', 'description' => 'Stock centralisé']
        )->id;
    }

    /**
     * Enregistrer une entrée en stock (réception).
     * Par défaut, les produits entrent en situation 'en_stock' (disponible dans la réserve).
     */
    public function enregistrerEntree(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $produit = Produit::findOrFail($data['produit_id']);
            $quantite = (int) ($data['quantite'] ?? 1);
            $destinationId = !empty($data['emplacement_id']) ? $data['emplacement_id'] : $this->getDefaultEmplacementId();
            $situation = $data['situation'] ?? Stock::SITUATION_EN_STOCK;
            $equipementId = $data['equipement_id'] ?? null;
            $equipement = null;

            if ($produit->isQuantite() || empty($equipementId)) {
                if ($quantite <= 0) {
                    throw ValidationException::withMessages([
                        'quantite' => 'La quantité reçue doit être strictement supérieure à zéro.',
                    ]);
                }

                // Lock and increment stock for (produit, emplacement, situation)
                $stock = Stock::firstOrCreate(
                    [
                        'produit_id' => $produit->id,
                        'emplacement_id' => $destinationId,
                        'situation' => $situation,
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
                'situation_depart' => null,
                'situation_arrivee' => $situation,
                'produit_id' => $produit->id,
                'equipement_id' => $equipement?->id,
                'quantite' => $quantite,
                'emplacement_source_id' => null,
                'emplacement_destination_id' => $destinationId,
                'motif' => $data['motif'] ?? 'Entrée en stock',
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
                newValues: ['type' => 'entree', 'quantite' => $quantite, 'produit' => $produit->designation, 'destination_id' => $destinationId, 'situation' => $situation],
                user: $user
            );

            return $mvt;
        });
    }

    /**
     * Enregistrer une sortie définitive du stock (mise au rebut, perte confirmée, etc.).
     * Le matériel quitte définitivement l'entreprise et réduit le stock global.
     */
    public function enregistrerSortie(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $produit = Produit::findOrFail($data['produit_id']);
            $quantite = (int) ($data['quantite'] ?? 1);
            $sourceId = $data['emplacement_id'] ?? null;
            $situation = $data['situation'] ?? Stock::SITUATION_EN_STOCK;
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
                    ->where(function ($q) use ($situation) {
                        if ($situation === Stock::SITUATION_EN_STOCK) {
                            $q->where('situation', Stock::SITUATION_EN_STOCK)->orWhereNull('situation');
                        } else {
                            $q->where('situation', $situation);
                        }
                    })
                    ->lockForUpdate()
                    ->first();

                $disponible = $stock ? $stock->quantite : 0;

                if ($disponible < $quantite) {
                    throw ValidationException::withMessages([
                        'quantite' => "Stock insuffisant pour cette sortie : seulement {$disponible} unité(s) disponibles.",
                    ]);
                }

                $stock->quantite -= $quantite;
                $stock->save();
                $sourceId = $stock->emplacement_id;
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
                'type' => Mouvement::TYPE_SORTIE_DEFINITIVE,
                'situation_depart' => $situation,
                'situation_arrivee' => null,
                'produit_id' => $produit->id,
                'equipement_id' => $equipement?->id,
                'quantite' => $quantite,
                'emplacement_source_id' => $sourceId,
                'emplacement_destination_id' => null,
                'motif' => $data['motif'] ?? 'Sortie de stock',
                'beneficiaire' => $data['beneficiaire'] ?? null,
                'commentaire' => $data['commentaire'] ?? null,
                'user_id' => $user->id,
                'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            ]);

            $motifLog = $data['motif'] ?? 'Sortie de stock';
            $this->activityLogger->log(
                module: 'stock',
                actionType: 'sortie',
                description: "Sortie définitive : {$quantite} x {$produit->nom} (Réf {$mvt->reference}) - {$motifLog}",
                entityType: 'Mouvement',
                entityId: $mvt->id,
                entityName: $mvt->reference,
                oldValues: null,
                newValues: ['type' => 'sortie_definitive', 'quantite' => $quantite, 'produit' => $produit->nom, 'source_id' => $sourceId, 'situation' => $situation],
                user: $user
            );

            return $mvt;
        });
    }

    /**
     * Changer la situation d'un matériel :
     * 1. En stock -> En utilisation (Mise en utilisation)
     * 2. En utilisation -> En stock (Retour en stock)
     * 3. En stock -> En panne (Déclaration de panne)
     * 4. En utilisation -> En panne (Déclaration de panne)
     * 5. En panne -> En stock (Remise en service avec confirmation)
     * 6. En panne -> En utilisation (Remise en service avec confirmation)
     *
     * Règles absolues :
     * - Quantité totale = En stock + En utilisation + En panne.
     * - Ne modifie jamais le total global du produit (conservation stricte des unités).
     * - Enregistre automatiquement la traçabilité dans l'historique des mouvements.
     */
    public function changerSituation(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $produit = Produit::findOrFail($data['produit_id']);
            $quantite = (int) ($data['quantite'] ?? 0);
            $situationDepart = $data['situation_depart'];
            $situationArrivee = $data['situation_arrivee'];
            $sourceEmplacementId = $data['emplacement_source_id'] ?? null;
            $destEmplacementId = $data['emplacement_destination_id'] ?? $sourceEmplacementId;

            // 1. Validation de base
            if ($quantite <= 0) {
                throw ValidationException::withMessages([
                    'quantite' => 'La quantité à déplacer doit être un entier strictement supérieur à zéro.',
                ]);
            }

            if ($situationDepart === $situationArrivee) {
                throw ValidationException::withMessages([
                    'situation_arrivee' => 'La nouvelle situation doit être différente de la situation actuelle.',
                ]);
            }

            $validSituations = [
                Stock::SITUATION_EN_STOCK,
                Stock::SITUATION_EN_UTILISATION,
                Stock::SITUATION_EN_PANNE,
            ];

            if (!in_array($situationDepart, $validSituations) || !in_array($situationArrivee, $validSituations)) {
                throw ValidationException::withMessages([
                    'situation' => 'Situation spécifiée invalide.',
                ]);
            }

            // Confirmation si remise en service depuis 'en_panne'
            if ($situationDepart === Stock::SITUATION_EN_PANNE) {
                $isConfirmed = !empty($data['confirme_fonctionnel']) && ($data['confirme_fonctionnel'] === true || $data['confirme_fonctionnel'] === '1' || $data['confirme_fonctionnel'] === 1);
                if (!$isConfirmed) {
                    throw ValidationException::withMessages([
                        'confirme_fonctionnel' => 'Veuillez confirmer que le matériel est de nouveau fonctionnel avant de le remettre en service.',
                    ]);
                }
            }

            // 2. Trouver et verrouiller le stock source
            $querySource = Stock::where('produit_id', $produit->id)
                ->where(function ($q) use ($situationDepart) {
                    if ($situationDepart === Stock::SITUATION_EN_STOCK) {
                        $q->where('situation', Stock::SITUATION_EN_STOCK)->orWhereNull('situation');
                    } else {
                        $q->where('situation', $situationDepart);
                    }
                });

            if ($sourceEmplacementId) {
                $querySource->where('emplacement_id', $sourceEmplacementId);
            }

            $sourceStock = $querySource->lockForUpdate()->first();
            $disponible = $sourceStock ? (int) $sourceStock->quantite : 0;

            if ($disponible < $quantite) {
                $departLabel = Stock::SITUATIONS[$situationDepart] ?? $situationDepart;
                throw ValidationException::withMessages([
                    'quantite' => "Stock insuffisant en situation « {$departLabel} » : seulement {$disponible} unité(s) disponible(s).",
                ]);
            }

            // Décrémenter la source
            $sourceStock->quantite -= $quantite;
            $sourceStock->save();

            // 3. Incrémenter la destination
            $targetLocationId = $destEmplacementId ?: ($sourceStock ? $sourceStock->emplacement_id : $this->getDefaultEmplacementId());

            $destStock = Stock::updateOrCreate(
                [
                    'produit_id' => $produit->id,
                    'emplacement_id' => $targetLocationId,
                    'situation' => $situationArrivee,
                ],
                []
            );
            if ($destStock->wasRecentlyCreated) {
                $destStock->quantite = 0;
                $destStock->save();
            }

            $destStock = Stock::where('id', $destStock->id)->lockForUpdate()->first();
            $destStock->quantite += $quantite;
            $destStock->save();

            // 4. Déterminer le type précis du mouvement
            $typeMouvement = match (true) {
                $situationDepart === Stock::SITUATION_EN_STOCK && $situationArrivee === Stock::SITUATION_EN_UTILISATION => Mouvement::TYPE_MISE_UTILISATION,
                $situationDepart === Stock::SITUATION_EN_UTILISATION && $situationArrivee === Stock::SITUATION_EN_STOCK => Mouvement::TYPE_RETOUR_STOCK,
                $situationArrivee === Stock::SITUATION_EN_PANNE => Mouvement::TYPE_DECLARATION_PANNE,
                $situationDepart === Stock::SITUATION_EN_PANNE => Mouvement::TYPE_REMISE_SERVICE,
                default => Mouvement::TYPE_CHANGEMENT_SITUATION,
            };

            // 5. Enregistrer le mouvement dans l'historique
            $mvt = Mouvement::create([
                'reference' => Mouvement::generateReference(),
                'type' => $typeMouvement,
                'situation_depart' => $situationDepart,
                'situation_arrivee' => $situationArrivee,
                'produit_id' => $produit->id,
                'equipement_id' => null,
                'quantite' => $quantite,
                'emplacement_source_id' => $sourceStock->emplacement_id,
                'emplacement_destination_id' => $targetLocationId,
                'motif' => $data['motif'] ?? "Changement de situation : {$situationDepart} vers {$situationArrivee}",
                'beneficiaire' => $data['beneficiaire'] ?? null,
                'commentaire' => $data['commentaire'] ?? null,
                'user_id' => $user->id,
                'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            ]);

            // 6. Journaliser l'activité
            $departNom = Stock::SITUATIONS[$situationDepart] ?? $situationDepart;
            $arriveeNom = Stock::SITUATIONS[$situationArrivee] ?? $situationArrivee;
            $this->activityLogger->log(
                module: 'stock',
                actionType: 'changement_situation',
                description: "Changement de situation : {$quantite} x {$produit->nom} ({$departNom} -> {$arriveeNom})",
                entityType: 'Mouvement',
                entityId: $mvt->id,
                entityName: $mvt->reference,
                oldValues: ['situation' => $situationDepart, 'emplacement_id' => $sourceStock->emplacement_id],
                newValues: ['situation' => $situationArrivee, 'emplacement_id' => $targetLocationId, 'quantite' => $quantite],
                user: $user
            );

            return $mvt;
        });
    }

    /**
     * Transférer du matériel entre deux emplacements distincts en conservant sa situation.
     */
    public function enregistrerTransfert(array $data, User $user): Mouvement
    {
        return DB::transaction(function () use ($data, $user) {
            $sourceId = $data['emplacement_source_id'];
            $destId = $data['emplacement_destination_id'];
            $situation = $data['situation'] ?? Stock::SITUATION_EN_STOCK;

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
                    ->where(function ($q) use ($situation) {
                        if ($situation === Stock::SITUATION_EN_STOCK) {
                            $q->where('situation', Stock::SITUATION_EN_STOCK)->orWhereNull('situation');
                        } else {
                            $q->where('situation', $situation);
                        }
                    })
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

                // Increment destination preserving the same situation
                $destStock = Stock::firstOrCreate(
                    [
                        'produit_id' => $produit->id,
                        'emplacement_id' => $destId,
                        'situation' => $situation,
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
                'situation_depart' => $situation,
                'situation_arrivee' => $situation,
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

            $emplacementId = $data['emplacement_id'] ?? $this->getDefaultEmplacementId();
            $situation = $data['situation'] ?? Stock::SITUATION_EN_STOCK;
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
                    'situation' => $situation,
                ],
                ['quantite' => 0]
            );

            $stock = Stock::where('id', $stock->id)->lockForUpdate()->first();
            $ancienneQuantite = $stock->quantite;
            $ecart = $nouvelleQuantite - $ancienneQuantite;

            $stock->quantite = $nouvelleQuantite;
            $stock->save();

            $signe = $ecart >= 0 ? "+{$ecart}" : (string) $ecart;
            $situationLabel = Stock::SITUATIONS[$situation] ?? $situation;
            $commentaireComplet = "Inventaire physique ({$situationLabel}) : ancienne valeur {$ancienneQuantite}, nouvelle valeur {$nouvelleQuantite} (Écart : {$signe}).\n" . ($data['commentaire'] ?? '');

            $mvt = Mouvement::create([
                'reference' => Mouvement::generateReference(),
                'type' => Mouvement::TYPE_CORRECTION,
                'situation_depart' => $situation,
                'situation_arrivee' => $situation,
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
                description: "Correction inventaire ({$situationLabel}) : {$produit->nom} à emplacement #{$emplacementId} (Ancienne: {$ancienneQuantite}, Nouvelle: {$nouvelleQuantite}, Écart: {$signe})",
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
