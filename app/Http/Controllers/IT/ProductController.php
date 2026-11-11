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
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $categoryId = $request->input('category_id');
        $etatStock = $request->string('etat_stock')->trim()->value(); // disponible, faible, rupture

        // 1. Calcul des 4 cartes statistiques (identiques au Dashboard)
        $totalProducts = Produit::where('statut', Produit::STATUT_ACTIF)->count();
        $totalUnitesStock = (int) Stock::whereHas('produit', fn ($q) => $q->where('statut', Produit::STATUT_ACTIF))->sum('quantite');

        // Calcul des produits actifs en stock faible (exactement 1) et rupture (exactement 0)
        $allActiveProducts = Produit::with('stocks')
            ->where('statut', Produit::STATUT_ACTIF)
            ->get();

        $stockFaibleCount = 0;
        $produitsEpuisesCount = 0;

        foreach ($allActiveProducts as $prod) {
            $qte = (int) $prod->stocks->sum('quantite');
            if ($qte === 0) {
                $produitsEpuisesCount++;
            } elseif ($qte === 1) {
                $stockFaibleCount++;
            }
        }

        // 2. Liste déroulante des catégories depuis la base de données
        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)
            ->orderBy('nom', 'asc')
            ->get();

        // 3. Construction de la requête filtrée
        $query = Produit::query()
            ->with(['categorie', 'stocks'])
            ->where('statut', Produit::STATUT_ACTIF);

        // Filtre Recherche : Nom, Marque, Référence
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

        // Filtre État du stock (disponible > 1, faible == 1, rupture == 0)
        if ($etatStock === 'rupture') {
            $query->whereDoesntHave('stocks', fn ($s) => $s->where('quantite', '>', 0));
        } elseif ($etatStock === 'faible') {
            $query->whereHas('stocks', function ($s) {
                $s->selectRaw('produit_id, sum(quantite) as total_qty')
                  ->groupBy('produit_id')
                  ->havingRaw('sum(quantite) = 1');
            });
        } elseif ($etatStock === 'disponible') {
            $query->whereHas('stocks', function ($s) {
                $s->selectRaw('produit_id, sum(quantite) as total_qty')
                  ->groupBy('produit_id')
                  ->havingRaw('sum(quantite) > 1');
            });
        }

        $products = $query->orderBy('nom', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('it.products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'totalUnitesStock',
            'stockFaibleCount',
            'produitsEpuisesCount',
            'search',
            'categoryId',
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

        $emplacements = \App\Models\Emplacement::where('statut', \App\Models\Emplacement::STATUT_ACTIF)
            ->orderBy('nom', 'asc')
            ->get();

        return view('it.products.create', compact('categories', 'emplacements'));
    }

    /**
     * Store a newly created product in storage with optional initial stock.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Valeurs par défaut simplifiées
        $validated['type_suivi'] = $validated['type_suivi'] ?? Produit::TYPE_QUANTITE;
        $validated['statut'] = $validated['statut'] ?? Produit::STATUT_ACTIF;

        // Générer une référence SKU automatique si non renseignée
        if (empty($validated['reference'])) {
            $validated['reference'] = 'REF-' . strtoupper(uniqid());
        }

        // Handle photo upload if present
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('products', 'public');
        }

        $quantiteInitiale = (int) ($request->input('quantite_initiale', 0));
        $emplacementId = $request->input('emplacement_id');

        // Récupérer un emplacement par défaut si non renseigné
        if (!$emplacementId) {
            $emplacementId = \App\Models\Emplacement::where('statut', \App\Models\Emplacement::STATUT_ACTIF)->value('id')
                ?? \App\Models\Emplacement::firstOrCreate(
                    ['nom' => 'Magasin Principal'],
                    ['code' => 'MAG-01', 'statut' => 'actif', 'description' => 'Emplacement principal par défaut']
                )->id;
        }

        $produit = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $quantiteInitiale, $emplacementId) {
            $produit = Produit::create($validated);

            // Créer la ligne de stock pour ce produit et emplacement
            $stock = Stock::firstOrCreate(
                [
                    'produit_id' => $produit->id,
                    'emplacement_id' => $emplacementId,
                ],
                ['quantite' => 0]
            );

            // Si une quantité initiale > 0 est fournie, enregistrer l'entrée et le mouvement
            if ($quantiteInitiale > 0) {
                $stock->quantite = $quantiteInitiale;
                $stock->save();

                // Enregistrer le mouvement dans l'historique
                \App\Models\Mouvement::create([
                    'reference' => \App\Models\Mouvement::generateReference(),
                    'type' => \App\Models\Mouvement::TYPE_ENTREE,
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

            return $produit;
        });

        return redirect()
            ->route('it.products.index')
            ->with('success', "Le matériel « {$produit->nom} » a été ajouté avec succès (stock initial : {$quantiteInitiale} unité(s)).");
    }

    /**
     * Display the specified product.
     */
    public function show(Produit $product): View
    {
        $product->load('categorie');

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

        // Handle photo replacement
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }

            $validated['photo'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('it.products.show', $product)
            ->with('success', "La référence « {$product->nom} » a été mise à jour avec succès.");
    }

    /**
     * Toggle status between active and archived.
     */
    public function toggleStatus(Request $request, Produit $product): RedirectResponse
    {
        $newStatus = $product->isActif() ? Produit::STATUT_ARCHIVE : Produit::STATUT_ACTIF;
        $product->update(['statut' => $newStatus]);

        $label = $newStatus === Produit::STATUT_ACTIF ? 'réactivée' : 'archivée';

        return back()->with('success', "La référence « {$product->nom} » est désormais {$label}.");
    }

    /**
     * Delete a product if allowed.
     */
    public function destroy(Produit $product): RedirectResponse
    {
        // Delete photo if exists
        if ($product->photo && Storage::disk('public')->exists($product->photo)) {
            Storage::disk('public')->delete($product->photo);
        }

        $nom = $product->nom;
        $product->delete();

        return redirect()
            ->route('it.products.index')
            ->with('success', "La référence « {$nom} » a été supprimée du catalogue.");
    }
}
