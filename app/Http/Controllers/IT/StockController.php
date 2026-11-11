<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
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
     * Display the stock overview (quantities, location breakdown, alerts).
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $categoryId = $request->input('category_id') ?? $request->input('categorie_id');
        $emplacementId = $request->input('emplacement_id');
        $stockAlert = $request->string('stock_alert')->trim()->value() ?: $request->string('alerte')->trim()->value();

        // Destinataire Gmail configuré pour les demandes de réapprovisionnement
        $directeurEmail = AppSetting::get('directeur_email', '');

        // 1. Calcul des 4 cartes statistiques & des alertes de stock (faible == 1, rupture == 0)
        $totalUnitesStock = (int) Stock::whereHas('produit', fn ($q) => $q->where('statut', Produit::STATUT_ACTIF))->sum('quantite');

        $allActiveProds = Produit::with(['categorie', 'stocks'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom', 'asc')
            ->get();

        $articlesEnStock = 0;
        $stockFaible = 0;
        $ruptureStock = 0;
        $alertesStock = [];

        foreach ($allActiveProds as $p) {
            $qte = (int) $p->stocks->sum('quantite');
            $nomProd = $p->nom ?? $p->designation;

            if ($qte === 0) {
                $ruptureStock++;

                $sujet = 'Demande de réapprovisionnement — ' . $nomProd;
                $corps = "Bonjour,\r\n\r\n"
                    . "Je vous sollicite pour une demande urgente de réapprovisionnement de matériel informatique :\r\n\r\n"
                    . "- Produit : {$nomProd}\r\n"
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
                    'nom' => $nomProd,
                    'quantite' => 0,
                    'type' => 'rupture', // Rouge
                    'gmail_url' => $gmailUrl,
                ];
            } elseif ($qte === 1) {
                $stockFaible++;

                $sujet = 'Demande de matériel informatique — ' . $nomProd;
                $corps = "Bonjour,\r\n\r\n"
                    . "Je vous soumets une demande de réapprovisionnement pour le matériel suivant en stock faible :\r\n\r\n"
                    . "- Produit : {$nomProd}\r\n"
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
                    'nom' => $nomProd,
                    'quantite' => 1,
                    'type' => 'faible', // Orange
                    'gmail_url' => $gmailUrl,
                ];
            } else {
                $articlesEnStock++;
            }
        }

        $kpis = [
            'total_unites_stock' => $totalUnitesStock,
            'articles_en_stock' => $articlesEnStock,
            'stock_faible' => $stockFaible,
            'rupture_stock' => $ruptureStock,
        ];

        // 2. Liste des stocks (Tous les produits actifs gérés en stock)
        $query = Produit::query()
            ->with(['categorie', 'stocks.emplacement'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nom', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('code_barre', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('categorie_id', $categoryId))
            ->when($emplacementId, function ($q) use ($emplacementId) {
                $q->whereHas('stocks', fn ($s) => $s->where('emplacement_id', $emplacementId)->where('quantite', '>', 0));
            });

        // Filtrage par alerte
        if ($stockAlert === 'rupture') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id) = 0');
        } elseif ($stockAlert === 'faible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id) = 1');
        } elseif ($stockAlert === 'normal' || $stockAlert === 'suffisant') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id) > 1');
        }

        $produits = $query->orderBy('nom', 'asc')->paginate(15)->withQueryString();
        $produitsQuantite = $produits; // Rétro-compatibilité

        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)->orderBy('nom')->get();
        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)->orderBy('nom')->get();

        return view('it.stocks.index', compact(
            'produits',
            'produitsQuantite',
            'alertesStock',
            'kpis',
            'categories',
            'emplacements',
            'search',
            'categoryId',
            'emplacementId',
            'stockAlert'
        ));
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

        return view('it.stocks.entree', compact('produits', 'equipements', 'equipementsIndividuels', 'emplacements'));
    }

    /**
     * Process stock entry.
     */
    public function storeEntree(EntreeStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $mouvement = $service->enregistrerEntree($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Entrée en stock enregistrée avec succès sous la référence « {$mouvement->reference} ».");
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

        return view('it.stocks.sortie', compact('produits', 'equipements', 'equipementsDisponibles', 'emplacements'));
    }

    /**
     * Process stock exit.
     */
    public function storeSortie(SortieStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $mouvement = $service->enregistrerSortie($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Sortie de stock validée sous la référence « {$mouvement->reference} ».");
    }

    /**
     * Show form for transferring stock between locations.
     */
    public function createTransfert(): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with(['categorie', 'stocks.emplacement'])
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $equipementsIndividuels = Equipement::with(['produit', 'emplacement'])
            ->where('statut', Equipement::STATUT_ACTIF)
            ->orderBy('code_inventaire')
            ->get();

        $equipements = $equipementsIndividuels;

        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        return view('it.stocks.transfert', compact('produits', 'equipements', 'equipementsIndividuels', 'emplacements'));
    }

    /**
     * Process stock transfer.
     */
    public function storeTransfert(TransfertStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $mouvement = $service->enregistrerTransfert($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Transfert inter-emplacements effectué sous la référence « {$mouvement->reference} ».");
    }

    /**
     * Show form for inventory correction.
     */
    public function createCorrection(): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with(['categorie', 'stocks.emplacement'])
            ->where('type_suivi', Produit::TYPE_QUANTITE)
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        return view('it.stocks.correction', compact('produits', 'emplacements'));
    }

    /**
     * Process inventory correction.
     */
    public function storeCorrection(CorrectionStockRequest $request, StockMovementService $service): RedirectResponse
    {
        $this->ensureTablesExist();

        $mouvement = $service->enregistrerCorrection($request->validated(), auth()->user());

        return redirect()
            ->route('it.stocks.index')
            ->with('success', "Régularisation d'inventaire validée sous la référence « {$mouvement->reference} ».");
    }
}
