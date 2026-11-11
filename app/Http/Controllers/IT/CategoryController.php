<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Http\Requests\IT\CategoryRequest;
use App\Models\Categorie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Ensure database tables exist before querying.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('categories')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);
            } catch (\Throwable $e) {
                // Ignore if migration already ran in parallel
            }
        }
    }

    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $statut = $request->string('statut')->trim()->value();

        $totalCategories = Categorie::count();
        $activeCategories = Categorie::where('statut', Categorie::STATUT_ACTIF)->count();
        $archivedCategories = Categorie::where('statut', Categorie::STATUT_ARCHIVE)->count();

        $categories = Categorie::query()
            ->withCount('produits')
            ->when($search, fn ($q) => $q->recherche($search))
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->orderBy('nom', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('it.categories.index', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'archivedCategories',
            'search',
            'statut'
        ));
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        Categorie::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'icon_name' => $request->icon_name ?: 'layers',
            'statut' => $request->statut,
        ]);

        return redirect()
            ->route('it.categories.index')
            ->with('success', "La catégorie « {$request->nom} » a été créée avec succès.");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Categorie $category): RedirectResponse
    {
        $category->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'icon_name' => $request->icon_name ?: 'layers',
            'statut' => $request->statut,
        ]);

        return redirect()
            ->route('it.categories.index')
            ->with('success', "La catégorie « {$category->nom} » a été mise à jour avec succès.");
    }

    /**
     * Toggle status between active and archived.
     */
    public function toggleStatus(Request $request, Categorie $category): RedirectResponse
    {
        $newStatus = $category->isActif() ? Categorie::STATUT_ARCHIVE : Categorie::STATUT_ACTIF;
        $category->update(['statut' => $newStatus]);

        $action = $newStatus === Categorie::STATUT_ACTIF ? 'réactivée' : 'archivée';

        return back()->with('success', "La catégorie « {$category->nom} » a été {$action} avec succès.");
    }

    /**
     * Remove the specified category from storage if it has no products.
     */
    public function destroy(Categorie $category): RedirectResponse
    {
        if ($category->produits()->count() > 0) {
            return back()->with('error', "Impossible de supprimer la catégorie « {$category->nom} » car elle contient encore des produits. Veuillez réaffecter les produits ou archiver la catégorie.");
        }

        $nom = $category->nom;
        $category->delete();

        return redirect()
            ->route('it.categories.index')
            ->with('success', "La catégorie « {$nom} » a été supprimée avec succès.");
    }
}
