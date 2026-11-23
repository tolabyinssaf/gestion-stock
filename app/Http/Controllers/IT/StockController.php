<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Http\Requests\IT\ChangementSituationRequest;
use App\Http\Requests\IT\CorrectionStockRequest;
use App\Http\Requests\IT\EntreeStockRequest;
use App\Http\Requests\IT\SortieStockRequest;
use App\Http\Requests\IT\TransfertStockRequest;
use App\Models\AppSetting;
use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\Produit;
use App\Models\Stock;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Ensure database tables exist before querying.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('stocks') || !Schema::hasTable('mouvements')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'EmplacementSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'StockMovementSeeder', '--force' => true]);
            } catch (\Throwable $e) {
                // Ignore if migration already ran in parallel
            }
        }
    }

    /**
     * Display the stock overview (quantities, situations, location breakdown, alerts).
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $categoryId = $request->input('category_id') ?? $request->input('categorie_id');
        $situation = $request->string('situation')->trim()->value();
        $stockAlert = $request->string('stock_alert')->trim()->value() ?: $request->string('alerte')->trim()->value();

        // Destinataire e-mail configuré pour les demandes de réapprovisionnement
        $directeurEmail = AppSetting::get('directeur_email', '');
        $directeurNom = AppSetting::get('directeur_nom', 'Direction Générale');
        $currentUser = auth()->user();
        $senderNom = $currentUser?->name ?? 'Gestionnaire de stock';
        $senderEmail = $currentUser?->email ?? '';

        // 1. Calcul global des unités par situation (Tous les matériels actifs)
        $allActiveProds = Produit::with(['categorie', 'stocks.emplacement'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom', 'asc')
            ->get();

        $totalEnStock = 0;
        $totalEnUtilisation = 0;
        $totalEnPanne = 0;
        $totalGlobal = 0;

        $articlesEnStock = 0;
        $stockFaible = 0;
        $ruptureStock = 0;
        $alertesStock = [];

        foreach ($allActiveProds as $p) {
            $qteEnStock = $p->quantite_en_stock;
            $qteUtil = $p->quantite_en_utilisation;
            $qtePanne = $p->quantite_en_panne;
            $qteTotale = $p->total_stock;

            $totalEnStock += $qteEnStock;
            $totalEnUtilisation += $qteUtil;
            $totalEnPanne += $qtePanne;
            $totalGlobal += $qteTotale;

            $seuil = ($p->seuil_minimum !== null && $p->seuil_minimum > 0) ? (int) $p->seuil_minimum : 1;
            $nomProd = $p->nom ?? $p->designation;

            if ($qteEnStock === 0) {
                $ruptureStock++;

                $sujet = 'Demande de réapprovisionnement — ' . $nomProd;
                $corps = "Bonjour {$directeurNom},\r\n\r\n"
                    . "Je vous sollicite pour une demande de réapprovisionnement de matériel informatique :\r\n\r\n"
                    . "■ MATÉRIEL CONCERNÉ :\r\n"
                    . "- Produit : {$nomProd}\r\n"
                    . "- Référence : {$p->reference}\r\n"
                    . "- Catégorie : " . ($p->categorie->nom ?? 'Non classé') . "\r\n"
                    . "- Quantité en stock (réserve) : 0 (ÉPUISÉ)\r\n"
                    . "- En utilisation : {$qteUtil}\r\n"
                    . "- En panne : {$qtePanne}\r\n"
                    . "- Quantité souhaitée : 5\r\n\r\n"
                    . "Merci de bien vouloir valider cette commande.\r\n\r\n"
                    . "Cordialement,\r\n"
                    . "{$senderNom}";

                $gmailUrl = 'https://mail.google.com/mail/?view=cm&fs=1'
                    . '&to=' . rawurlencode($directeurEmail)
                    . '&su=' . rawurlencode($sujet)
                    . '&body=' . rawurlencode($corps);

                $alertesStock[] = [
                    'produit' => $p,
                    'nom' => $nomProd,
                    'quantite' => 0,
                    'seuil' => $seuil,
                    'type' => 'rupture',
                    'gmail_url' => $gmailUrl,
                ];
            } elseif ($qteEnStock <= $seuil) {
                $stockFaible++;

                $sujet = 'Alerte stock faible — ' . $nomProd;
                $corps = "Bonjour {$directeurNom},\r\n\r\n"
                    . "Je vous soumets une demande de réapprovisionnement pour le matériel suivant en stock faible :\r\n\r\n"
                    . "■ MATÉRIEL CONCERNÉ :\r\n"
                    . "- Produit : {$nomProd}\r\n"
                    . "- Référence : {$p->reference}\r\n"
                    . "- Catégorie : " . ($p->categorie->nom ?? 'Non classé') . "\r\n"
                    . "- Quantité en stock (réserve) : {$qteEnStock} unité(s) restante(s)\r\n"
                    . "- En utilisation : {$qteUtil}\r\n"
                    . "- En panne : {$qtePanne}\r\n"
                    . "- Seuil d'alerte : {$seuil}\r\n"
                    . "- Quantité souhaitée : 5\r\n\r\n"
                    . "Merci de bien vouloir étudier cette demande.\r\n\r\n"
                    . "Cordialement,\r\n"
                    . "{$senderNom}";

                $gmailUrl = 'https://mail.google.com/mail/?view=cm&fs=1'
                    . '&to=' . rawurlencode($directeurEmail)
                    . '&su=' . rawurlencode($sujet)
                    . '&body=' . rawurlencode($corps);

                $alertesStock[] = [
                    'produit' => $p,
                    'nom' => $nomProd,
                    'quantite' => $qteEnStock,
                    'seuil' => $seuil,
                    'type' => 'faible',
                    'gmail_url' => $gmailUrl,
                ];
            } else {
                $articlesEnStock++;
            }
        }

        $kpis = [
            'total_en_stock' => $totalEnStock,
            'total_en_utilisation' => $totalEnUtilisation,
            'total_en_panne' => $totalEnPanne,
            'total_global' => $totalGlobal,
            'articles_en_stock' => $articlesEnStock,
            'stock_faible' => $stockFaible,
            'rupture_stock' => $ruptureStock,
        ];

        // 2. Requête filtrée pour l'affichage paginé
        $query = Produit::query()
            ->with(['categorie', 'stocks.emplacement'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nom', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('marque', 'like', "%{$search}%")
                        ->orWhere('modele', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('categorie_id', $categoryId))
            ->when($situation && $situation !== 'tous', function ($q) use ($situation) {
                $q->enSituation($situation);
            });

        // Filtrage par alerte selon quantite_en_stock vs seuil_minimum
        if ($stockAlert === 'rupture') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) = 0');
        } elseif ($stockAlert === 'faible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) > 0')
                  ->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) <= COALESCE(NULLIF(produits.seuil_minimum, 0), 1)');
        } elseif ($stockAlert === 'normal' || $stockAlert === 'suffisant') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) > COALESCE(NULLIF(produits.seuil_minimum, 0), 1)');
        }

        $produits = $query->orderBy('nom', 'asc')->paginate(10)->withQueryString();
        $produitsQuantite = $produits; // Rétro-compatibilité

        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)->orderBy('nom')->get();
        $tousProduits = Produit::where('statut', Produit::STATUT_ACTIF)->orderBy('nom')->get(['id', 'nom', 'reference']);

        return view('it.stocks.index', compact(
            'produits',
            'produitsQuantite',
            'alertesStock',
            'kpis',
            'categories',
            'tousProduits',
            'search',
            'categoryId',
            'situation',
            'stockAlert'
        ));
    }

    /**
     * Changer la situation d'un matériel.
     */
    public function storeChangementSituation(ChangementSituationRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        // 🔍 DIAGNOSTIC TEMPORAIRE — supprimer après résolution du problème
        \Log::info('CHANGEMENT SITUATION - Payload reçu', [
            'validated' => $request->validated(),
            'raw'       => $request->all(),
        ]);

        $service->changerSituation($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', 'Situation du matériel mise à jour.');
    }

    /**
     * Show form for registering a new stock entry.
     */
    public function createEntree(): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with('categorie')
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $equipementsIndividuels = Equipement::with('produit')
            ->where('statut', Equipement::STATUT_ACTIF)
            ->orderBy('code_inventaire')
            ->get();

        $equipements = $equipementsIndividuels;

        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        // Date "maintenant" dans le fuseau applicatif, pour préremplir le champ
        $nowLocal = now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i');

        return view('it.stocks.entree', compact(
            'produits',
            'equipements',
            'equipementsIndividuels',
            'emplacements',
            'nowLocal'
        ));
    }

    /**
     * Process stock entry.
     */
    public function storeEntree(EntreeStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $service->enregistrerEntree($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Entrée enregistrée.");
    }

    /**
     * Show form for registering a stock exit.
     */
    public function createSortie(): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with(['categorie', 'stocks.emplacement'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $equipementsDisponibles = Equipement::with(['produit', 'emplacement'])
            ->where('statut', Equipement::STATUT_ACTIF)
            ->where('statut_utilisation', Equipement::STATUT_DISPONIBLE)
            ->orderBy('code_inventaire')
            ->get();

        $equipements = $equipementsDisponibles;

        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $nowLocal = now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i');

        return view('it.stocks.sortie', compact(
            'produits',
            'equipements',
            'equipementsDisponibles',
            'emplacements',
            'nowLocal'
        ));
    }

    /**
     * Process stock exit.
     */
    public function storeSortie(SortieStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $service->enregistrerSortie($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Sortie enregistrée.");
    }

    /**
     * Show form for transferring stock (redirected to index as locations have been removed in favor of situations).
     */
    public function createTransfert(): RedirectResponse
    {
        return redirect()->route('it.stocks.index');
    }

    /**
     * Process stock transfer.
     */
    public function storeTransfert(TransfertStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $service->enregistrerTransfert($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Transfert enregistré.");
    }

    /**
     * Show form for inventory correction.
     */
    public function createCorrection(): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with(['categorie', 'stocks.emplacement'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $nowLocal = now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i');

        return view('it.stocks.correction', compact('produits', 'emplacements', 'nowLocal'));
    }

    /**
     * Process inventory correction.
     */
    public function storeCorrection(CorrectionStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $service->enregistrerCorrection($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Correction du stock validée.");
    }
}