<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Http\Requests\IT\StoreProductRequest;
use App\Http\Requests\IT\UpdateProductRequest;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Ensure database tables exist before querying.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('produits') || !Schema::hasTable('categories')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);
            } catch (\Throwable $e) {
                // Ignore if migration already ran in parallel
            }
        }
    }

    /**
     * Display a listing of products with situation breakdowns.
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $categoryId = $request->input('category_id');
        $situation = $request->string('situation')->trim()->value(); // en_stock, en_utilisation, en_panne
        $etatStock = $request->string('etat_stock')->trim()->value(); // disponible, faible, rupture

        // 1. Calcul des 4 cartes statistiques par situation
        $totalProducts = Produit::count();

        $allActiveProds = Produit::with('stocks')->get();

        $totalEnStock = 0;
        $totalEnUtilisation = 0;
        $totalEnPanne = 0;

        foreach ($allActiveProds as $prod) {
            $totalEnStock += $prod->quantite_en_stock;
            $totalEnUtilisation += $prod->quantite_en_utilisation;
            $totalEnPanne += $prod->quantite_en_panne;
        }

        $totalUnitesStock = $totalEnStock + $totalEnUtilisation + $totalEnPanne;

        // 2. Catégories actives
        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)
            ->orderBy('nom', 'asc')
            ->get();

        // 3. Construction de la requête filtrée
        $query = Produit::query()
            ->with(['categorie', 'stocks']);

        // Filtre Recherche
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('marque', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('modele', 'like', "%{$search}%");
            });
        }

        // Filtre Catégorie
        if ($categoryId) {
            $query->where('categorie_id', $categoryId);
        }

        // Filtre Situation (Un produit apparaît si au moins 1 unité possède cette situation)
        if ($situation && $situation !== 'tous') {
            $query->enSituation($situation);
        }

        // Filtre Niveau de stock (selon unités En stock vs seuil_minimum)
        if ($etatStock === 'rupture') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) = 0');
        } elseif ($etatStock === 'faible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) > 0')
                  ->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) <= COALESCE(NULLIF(produits.seuil_minimum, 0), 1)');
        } elseif ($etatStock === 'disponible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) > COALESCE(NULLIF(produits.seuil_minimum, 0), 1)');
        }

        $products = $query->orderBy('nom', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('it.products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'totalEnStock',
            'totalEnUtilisation',
            'totalEnPanne',
            'totalUnitesStock',
            'search',
            'categoryId',
            'situation',
            'etatStock'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $this->ensureTablesExist();

        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)
            ->orderBy('nom', 'asc')
            ->get();

        return view('it.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage with optional initial stock.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['type_suivi'] = $validated['type_suivi'] ?? Produit::TYPE_QUANTITE;
        $validated['statut'] = $validated['statut'] ?? Produit::STATUT_ACTIF;

        // Prevent NULL on NOT NULL columns — fallback to empty string
        $validated['marque'] = $validated['marque'] ?? '';
        $validated['modele'] = $validated['modele'] ?? '';

        if (empty($validated['reference'])) {
            $validated['reference'] = 'REF-' . strtoupper(uniqid());
        }

        // Gestion de l'upload d'image
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['photo'] = $path;

            // Mirror to public/storage/products for maximum web server compatibility
            try {
                $publicTarget = public_path('storage/' . $path);
                if (!is_dir(dirname($publicTarget))) {
                    @mkdir(dirname($publicTarget), 0777, true);
                }
                @copy(storage_path('app/public/' . $path), $publicTarget);
            } catch (\Throwable $e) {
                // Silently ignore mirror copy failures
            }
        } else {
            // Évite d'écrire une valeur null/vide dans la colonne photo
            unset($validated['photo']);
        }

        $quantiteInitiale = (int) ($request->input('quantite_initiale', 0));
        $emplacementId = $request->input('emplacement_id');

        if (!$emplacementId) {
            $emplacementId = \App\Models\Emplacement::where('statut', \App\Models\Emplacement::STATUT_ACTIF)->value('id')
                ?? \App\Models\Emplacement::firstOrCreate(
                    ['nom' => 'Réserve IT'],
                    ['code' => 'LOC-RES-IT', 'statut' => 'actif', 'description' => 'Local technique sécurisé']
                )->id;
        }

        DB::transaction(function () use ($validated, $quantiteInitiale, $emplacementId) {
            $produit = Produit::create($validated);

            // Créer la ligne de stock pour ce produit en situation 'en_stock'
            $stock = Stock::firstOrCreate(
                [
                    'produit_id' => $produit->id,
                    'emplacement_id' => $emplacementId,
                    'situation' => Stock::SITUATION_EN_STOCK,
                ],
                ['quantite' => 0]
            );

            if ($quantiteInitiale > 0) {
                $stock->quantite = $quantiteInitiale;
                $stock->save();

                \App\Models\Mouvement::create([
                    'reference' => \App\Models\Mouvement::generateReference(),
                    'type' => \App\Models\Mouvement::TYPE_ENTREE,
                    'situation_depart' => null,
                    'situation_arrivee' => Stock::SITUATION_EN_STOCK,
                    'produit_id' => $produit->id,
                    'equipement_id' => null,
                    'quantite' => $quantiteInitiale,
                    'emplacement_source_id' => null,
                    'emplacement_destination_id' => $emplacementId,
                    'motif' => 'Stock initial à la création du produit',
                    'beneficiaire' => null,
                    'commentaire' => 'Initialisation automatique du stock',
                    'user_id' => auth()->id() ?? 1,
                    'date_mouvement' => now()->toDateString(),
                ]);
            }
        });

        return redirect()
            ->route('it.products.index')
            ->with('success', "Matériel ajouté avec succès.");
    }

    /**
     * Display the specified product.
     */
    public function show(Produit $product): View
    {
        $product->load(['categorie', 'stocks.emplacement']);

        return view('it.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Produit $product): View
    {
        $categories = Categorie::orderBy('nom', 'asc')->get();

        return view('it.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Produit $product): RedirectResponse
    {
        $validated = $request->validated();

        // Prevent NULL on NOT NULL columns
        $validated['marque'] = $validated['marque'] ?? '';
        $validated['modele'] = $validated['modele'] ?? '';

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne image (disque public + miroir)
            if ($product->photo) {
                if (Storage::disk('public')->exists($product->photo)) {
                    Storage::disk('public')->delete($product->photo);
                }
                $publicOld = public_path('storage/' . $product->photo);
                if (file_exists($publicOld)) {
                    @unlink($publicOld);
                }
            }

            $path = $request->file('photo')->store('products', 'public');
            $validated['photo'] = $path;

            // Mirror to public/storage/products
            try {
                $publicTarget = public_path('storage/' . $path);
                if (!is_dir(dirname($publicTarget))) {
                    @mkdir(dirname($publicTarget), 0777, true);
                }
                @copy(storage_path('app/public/' . $path), $publicTarget);
            } catch (\Throwable $e) {
                // Silently ignore mirror copy failures
            }
        } else {
            // Conserver l'image existante : ne pas écraser la colonne photo
            unset($validated['photo']);
        }

        $product->update($validated);

        return redirect()
            ->route('it.products.show', $product)
            ->with('success', "Matériel mis à jour avec succès.");
    }

    /**
     * Toggle status between active and archived.
     */
    public function toggleStatus(Request $request, Produit $product): RedirectResponse
    {
        $newStatus = $product->isActif() ? Produit::STATUT_ARCHIVE : Produit::STATUT_ACTIF;
        $product->update(['statut' => $newStatus]);

        return back()->with('success', "Statut du matériel mis à jour.");
    }

    /**
     * Delete a product if allowed.
     */
    public function destroy(Produit $product): RedirectResponse
    {
        // Supprimer l'image associée (disque + miroir public)
        if ($product->photo) {
            if (Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }
            $publicOld = public_path('storage/' . $product->photo);
            if (file_exists($publicOld)) {
                @unlink($publicOld);
            }
        }

        $product->delete();

        return redirect()
            ->route('it.products.index')
            ->with('success', "Matériel supprimé avec succès.");
    }
}