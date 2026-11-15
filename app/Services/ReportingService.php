<?php

namespace App\Services;

use App\Models\DemandeMateriel;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportingService
{
    /**
     * Get real KPIs for IT Dashboard (Focus: Stock Management & Situations).
     */
    public function getItDashboardStats(): array
    {
        $hasProduits = Schema::hasTable('produits');
        $hasStocks = Schema::hasTable('stocks');
        $hasMouvements = Schema::hasTable('mouvements');

        $directeurEmail = \App\Models\AppSetting::get('directeur_email', '');
        $directeurNom = \App\Models\AppSetting::get('directeur_nom', 'Direction Générale');
        $currentUser = auth()->user();
        $senderNom = $currentUser?->name ?? 'Gestionnaire de stock';
        $senderEmail = $currentUser?->email ?? '';

        // 1. Produits enregistrés
        $totalProduits = $hasProduits ? Produit::where('statut', Produit::STATUT_ACTIF)->count() : 0;

        // 2. Calcul des quantités par situation
        $totalEnStock = 0;
        $totalEnUtilisation = 0;
        $totalEnPanne = 0;
        $stockFaibleCount = 0;
        $produitsEpuisesCount = 0;
        $alertesStock = [];

        if ($hasProduits) {
            $produits = Produit::with(['categorie', 'stocks.emplacement'])
                ->where('statut', Produit::STATUT_ACTIF)
                ->orderBy('nom', 'asc')
                ->get();

            foreach ($produits as $p) {
                $qteStock = $p->quantite_en_stock;
                $qteUtil = $p->quantite_en_utilisation;
                $qtePanne = $p->quantite_en_panne;

                $totalEnStock += $qteStock;
                $totalEnUtilisation += $qteUtil;
                $totalEnPanne += $qtePanne;

                $seuil = ($p->seuil_minimum !== null && $p->seuil_minimum > 0) ? (int) $p->seuil_minimum : 1;

                if ($qteStock === 0) {
                    $produitsEpuisesCount++;

                    $sujet = 'Demande de réapprovisionnement — ' . $p->nom;
                    $corps = "==================================================\r\n"
                        . "DE (Expéditeur)   : {$senderNom} <{$senderEmail}> (Gestionnaire de stock)\r\n"
                        . "À  (Destinataire) : {$directeurNom} <{$directeurEmail}>\r\n"
                        . "DATE              : " . now()->format('d/m/Y à H:i') . "\r\n"
                        . "OBJET             : {$sujet}\r\n"
                        . "==================================================\r\n\r\n"
                        . "Bonjour {$directeurNom},\r\n\r\n"
                        . "Je vous sollicite pour une demande urgente de réapprovisionnement de matériel informatique :\r\n\r\n"
                        . "■ MATÉRIEL CONCERNÉ :\r\n"
                        . "- Produit : {$p->nom}\r\n"
                        . "- Référence : {$p->reference}\r\n"
                        . "- Catégorie : " . ($p->categorie->nom ?? 'Non classé') . "\r\n"
                        . "- Quantité en stock (réserve) : 0 (ÉPUISÉ)\r\n"
                        . "- En utilisation : {$qteUtil}\r\n"
                        . "- En panne : {$qtePanne}\r\n"
                        . "- Quantité souhaitée : 5\r\n\r\n"
                        . "Merci de bien vouloir valider cette commande.\r\n\r\n"
                        . "Cordialement,\r\n\r\n"
                        . "--------------------------------------------------\r\n"
                        . "{$senderNom}\r\n"
                        . "Gestionnaire de stock — Service Informatique\r\n"
                        . "VAR du Lassureur\r\n"
                        . "E-mail : {$senderEmail}";

                    $gmailUrl = 'https://mail.google.com/mail/?view=cm&fs=1'
                        . '&to=' . rawurlencode($directeurEmail)
                        . '&su=' . rawurlencode($sujet)
                        . '&body=' . rawurlencode($corps);

                    $alertesStock[] = [
                        'produit' => $p,
                        'quantite' => 0,
                        'seuil' => $seuil,
                        'type' => 'rupture', // rouge
                        'gmail_url' => $gmailUrl,
                    ];
                } elseif ($qteStock <= $seuil) {
                    $stockFaibleCount++;

                    $sujet = 'Alerte stock faible — ' . $p->nom;
                    $corps = "==================================================\r\n"
                        . "DE (Expéditeur)   : {$senderNom} <{$senderEmail}> (Gestionnaire de stock)\r\n"
                        . "À  (Destinataire) : {$directeurNom} <{$directeurEmail}>\r\n"
                        . "DATE              : " . now()->format('d/m/Y à H:i') . "\r\n"
                        . "OBJET             : {$sujet}\r\n"
                        . "==================================================\r\n\r\n"
                        . "Bonjour {$directeurNom},\r\n\r\n"
                        . "Je vous soumets une demande de réapprovisionnement pour le matériel suivant en stock faible :\r\n\r\n"
                        . "■ MATÉRIEL CONCERNÉ :\r\n"
                        . "- Produit : {$p->nom}\r\n"
                        . "- Référence : {$p->reference}\r\n"
                        . "- Catégorie : " . ($p->categorie->nom ?? 'Non classé') . "\r\n"
                        . "- Quantité en stock (réserve) : {$qteStock} unité(s) restante(s)\r\n"
                        . "- En utilisation : {$qteUtil}\r\n"
                        . "- En panne : {$qtePanne}\r\n"
                        . "- Seuil minimum d'alerte : {$seuil}\r\n"
                        . "- Quantité souhaitée : 5\r\n\r\n"
                        . "Merci de bien vouloir étudier cette demande.\r\n\r\n"
                        . "Cordialement,\r\n\r\n"
                        . "--------------------------------------------------\r\n"
                        . "{$senderNom}\r\n"
                        . "Gestionnaire de stock — Service Informatique\r\n"
                        . "VAR du Lassureur\r\n"
                        . "E-mail : {$senderEmail}";

                    $gmailUrl = 'https://mail.google.com/mail/?view=cm&fs=1'
                        . '&to=' . rawurlencode($directeurEmail)
                        . '&su=' . rawurlencode($sujet)
                        . '&body=' . rawurlencode($corps);

                    $alertesStock[] = [
                        'produit' => $p,
                        'quantite' => $qteStock,
                        'seuil' => $seuil,
                        'type' => 'faible', // orange
                        'gmail_url' => $gmailUrl,
                    ];
                }
            }
        }

        $totalUnitesStock = $totalEnStock + $totalEnUtilisation + $totalEnPanne;

        // 3. Les 5 derniers mouvements
        $recentMovements = [];
        if ($hasMouvements) {
            $recentMovements = Mouvement::with(['produit', 'user', 'emplacementSource', 'emplacementDestination'])
                ->latest('created_at')
                ->take(5)
                ->get();
        }

        return [
            'totalProduits' => $totalProduits,
            'totalEnStock' => $totalEnStock,
            'totalEnUtilisation' => $totalEnUtilisation,
            'totalEnPanne' => $totalEnPanne,
            'totalUnitesStock' => $totalUnitesStock,
            'stockFaibleCount' => $stockFaibleCount,
            'produitsEpuisesCount' => $produitsEpuisesCount,
            'alertesStock' => $alertesStock,
            'recentMovements' => $recentMovements,
            'directeurEmail' => $directeurEmail,
        ];
    }

    /**
     * Get real KPIs for Admin Dashboard.
     */
    public function getAdminDashboardStats(): array
    {
        $hasUsers = Schema::hasTable('users');
        $hasEquipements = Schema::hasTable('equipements');
        $hasProduits = Schema::hasTable('produits');
        $hasMouvements = Schema::hasTable('mouvements');

        // Utilisateurs
        $totalUsers = $hasUsers ? User::count() : 0;
        $activeUsers = $hasUsers ? User::where('status', User::STATUS_ACTIVE)->count() : 0;
        $inactiveUsers = $hasUsers ? User::where('status', User::STATUS_INACTIVE)->count() : 0;
        $adminCount = $hasUsers ? User::where('role', User::ROLE_ADMIN)->count() : 0;
        $itCount = $hasUsers ? User::where('role', User::ROLE_RESPONSABLE_IT)->count() : 0;

        // Supervision Stock
        $totalProduits = $hasProduits ? Produit::where('statut', Produit::STATUT_ACTIF)->count() : 0;
        $totalEquipements = Schema::hasTable('stocks') ? (int) Stock::sum('quantite') : ($hasEquipements ? Equipement::count() : 0);

        $ruptureCount = 0;
        if ($hasProduits && Schema::hasTable('stocks')) {
            $produits = Produit::with('stocks')
                ->where('statut', Produit::STATUT_ACTIF)
                ->get();

            foreach ($produits as $p) {
                if ($p->quantite_en_stock === 0) {
                    $ruptureCount++;
                }
            }
        }

        // Activité récente (mouvements + utilisateurs)
        $recentActivities = [];
        if ($hasMouvements) {
            $recentMovements = Mouvement::with(['produit', 'user'])
                ->latest('created_at')
                ->take(5)
                ->get();

            foreach ($recentMovements as $m) {
                $recentActivities[] = [
                    'type' => 'mouvement',
                    'titre' => 'Mouvement de stock (' . ($m->type_label ?? ucfirst($m->type)) . ')',
                    'description' => ($m->produit->nom ?? 'Matériel') . ' — Qté: ' . $m->quantite . ' par ' . ($m->user->name ?? 'Système'),
                    'date' => $m->created_at,
                    'badge' => $m->type,
                ];
            }
        }

        if ($hasUsers) {
            $recentUsers = User::latest('created_at')->take(3)->get();
            foreach ($recentUsers as $u) {
                $recentActivities[] = [
                    'type' => 'user',
                    'titre' => 'Compte utilisateur créé',
                    'description' => $u->name . ' (' . $u->email . ') — Rôle: ' . $u->role_label,
                    'date' => $u->created_at,
                    'badge' => 'compte',
                ];
            }
        }

        usort($recentActivities, fn($a, $b) => $b['date'] <=> $a['date']);
        $recentActivities = array_slice($recentActivities, 0, 8);

        return [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'adminCount' => $adminCount,
            'itCount' => $itCount,
            'totalProduits' => $totalProduits,
            'totalEquipements' => $totalEquipements,
            'ruptureCount' => $ruptureCount,
            'recentActivities' => $recentActivities,
        ];
    }
}
