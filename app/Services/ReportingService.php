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
     * Get real KPIs for IT Dashboard (Focus: Stock Management).
     */
    public function getItDashboardStats(): array
    {
        $hasProduits = Schema::hasTable('produits');
        $hasStocks = Schema::hasTable('stocks');
        $hasMouvements = Schema::hasTable('mouvements');

        // Récupérer le destinataire Gmail configuré pour les demandes
        $directeurEmail = \App\Models\AppSetting::get('directeur_email', '');

        // 1. Produits actifs au catalogue
        $totalProduits = $hasProduits ? Produit::where('statut', Produit::STATUT_ACTIF)->count() : 0;

        // 2. Calcul des quantités réelles par produit actif
        $totalUnitesStock = 0;
        $stockFaibleCount = 0;
        $produitsEpuisesCount = 0;
        $alertesStock = [];

        if ($hasProduits) {
            $produits = Produit::with(['categorie', 'stocks'])
                ->where('statut', Produit::STATUT_ACTIF)
                ->orderBy('nom', 'asc')
                ->get();

            foreach ($produits as $p) {
                // Somme des quantités disponibles dans tous les emplacements
                $qte = (int) $p->stocks->sum('quantite');
                $totalUnitesStock += $qte;

                if ($qte === 0) {
                    $produitsEpuisesCount++;

                    // Construction de l'URL Gmail préremplie
                    $sujet = 'Demande de réapprovisionnement — ' . $p->nom;
                    $corps = "Bonjour,\r\n\r\n"
                        . "Je vous sollicite pour une demande urgente de réapprovisionnement de matériel informatique :\r\n\r\n"
                        . "- Produit : {$p->nom}\r\n"
                        . "- Référence : {$p->reference}\r\n"
                        . "- Catégorie : " . ($p->categorie->nom ?? 'Non classé') . "\r\n"
                        . "- Quantité restante en stock : 0 (RUPTURE)\r\n"
                        . "- Quantité souhaitée : 5\r\n\r\n"
                        . "Merci de bien vouloir valider cette commande.\r\n\r\n"
                        . "Cordialement,\r\n"
                        . (auth()->user()?->name ?? 'Responsable IT');

                    $gmailUrl = 'https://mail.google.com/mail/?view=cm&fs=1'
                        . '&to=' . rawurlencode($directeurEmail)
                        . '&su=' . rawurlencode($sujet)
                        . '&body=' . rawurlencode($corps);

                    $alertesStock[] = [
                        'produit' => $p,
                        'quantite' => 0,
                        'type' => 'rupture', // rouge
                        'gmail_url' => $gmailUrl,
                    ];
                } elseif ($qte === 1) {
                    $stockFaibleCount++;

                    // Construction de l'URL Gmail préremplie
                    $sujet = 'Demande de matériel informatique — ' . $p->nom;
                    $corps = "Bonjour,\r\n\r\n"
                        . "Je vous soumets une demande de réapprovisionnement pour le matériel suivant en stock faible :\r\n\r\n"
                        . "- Produit : {$p->nom}\r\n"
                        . "- Référence : {$p->reference}\r\n"
                        . "- Catégorie : " . ($p->categorie->nom ?? 'Non classé') . "\r\n"
                        . "- Quantité restante en stock : 1 unité restante\r\n"
                        . "- Quantité souhaitée : 5\r\n\r\n"
                        . "Merci de bien vouloir étudier cette demande.\r\n\r\n"
                        . "Cordialement,\r\n"
                        . (auth()->user()?->name ?? 'Responsable IT');

                    $gmailUrl = 'https://mail.google.com/mail/?view=cm&fs=1'
                        . '&to=' . rawurlencode($directeurEmail)
                        . '&su=' . rawurlencode($sujet)
                        . '&body=' . rawurlencode($corps);

                    $alertesStock[] = [
                        'produit' => $p,
                        'quantite' => 1,
                        'type' => 'faible', // orange
                        'gmail_url' => $gmailUrl,
                    ];
                }
            }
        }

        // 3. Les 5 derniers mouvements
        $recentMovements = [];
        if ($hasMouvements) {
            $recentMovements = Mouvement::with(['produit', 'user', 'emplacementSource', 'emplacementDestination'])
                ->latest('date_mouvement')
                ->take(5)
                ->get();
        }

        return [
            'totalProduits' => $totalProduits,
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

        // Supervision IT
        $totalEquipements = $hasEquipements ? Equipement::count() : 0;
        $totalProduits = $hasProduits ? Produit::where('statut', Produit::STATUT_ACTIF)->count() : 0;

        // Ruptures de stock
        $ruptureCount = 0;
        if ($hasProduits && Schema::hasTable('stocks')) {
            $produits = Produit::with('stocks')
                ->where('type_suivi', Produit::TYPE_QUANTITE)
                ->where('statut', Produit::STATUT_ACTIF)
                ->get();

            foreach ($produits as $p) {
                if ($p->total_stock === 0) {
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
                    'titre' => 'Mouvement de stock (' . ucfirst($m->type) . ')',
                    'description' => ($m->produit->designation ?? 'Article') . ' — Qté: ' . $m->quantite . ' par ' . ($m->user->name ?? 'Système'),
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

        // Trier par date descendante
        usort($recentActivities, fn($a, $b) => $b['date'] <=> $a['date']);
        $recentActivities = array_slice($recentActivities, 0, 8);

        return [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'adminCount' => $adminCount,
            'itCount' => $itCount,
            'totalEquipements' => $totalEquipements,
            'totalProduits' => $totalProduits,
            'ruptureCount' => $ruptureCount,
            'recentActivities' => $recentActivities,
        ];
    }
}
