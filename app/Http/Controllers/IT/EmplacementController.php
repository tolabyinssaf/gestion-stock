<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Http\Requests\IT\EmplacementRequest;
use App\Models\Emplacement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EmplacementController extends Controller
{
    /**
     * Ensure database tables exist before querying.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('emplacements')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'EmplacementSeeder', '--force' => true]);
            } catch (\Throwable $e) {
                // Ignore if migration already ran in parallel
            }
        }
    }

    /**
     * Display a listing of locations with equipment and stock counts.
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $statut = $request->string('statut')->trim()->value();

        $totalEmplacements = Emplacement::count();
        $actifsCount = Emplacement::where('statut', Emplacement::STATUT_ACTIF)->count();
        $archivesCount = Emplacement::where('statut', Emplacement::STATUT_ARCHIVE)->count();

        $emplacements = Emplacement::query()
            ->withCount(['equipements', 'stocks as stocks_count' => function ($q) {
                $q->where('quantite', '>', 0);
            }])
            ->withSum('stocks as total_unites_stock', 'quantite')
            ->when($search, fn ($q) => $q->recherche($search))
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view('it.emplacements.index', compact(
            'emplacements',
            'totalEmplacements',
            'actifsCount',
            'archivesCount',
            'search',
            'statut'
        ));
    }

    /**
     * Store a newly created location.
     */
    public function store(EmplacementRequest $request): RedirectResponse
    {
        $this->ensureTablesExist();

        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));

        $emplacement = Emplacement::create($data);

        return redirect()
            ->route('it.emplacements.index')
            ->with('success', "L'emplacement « {$emplacement->nom} » ({$emplacement->code}) a été créé avec succès.");
    }

    /**
     * Display location details with present equipment and stock inventory.
     */
    public function show(Emplacement $emplacement): View
    {
        $this->ensureTablesExist();

        $emplacement->load([
            'equipements.produit.categorie',
            'stocks.produit.categorie',
        ]);

        return view('it.emplacements.show', compact('emplacement'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(EmplacementRequest $request, Emplacement $emplacement): RedirectResponse
    {
        $this->ensureTablesExist();

        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));

        $emplacement->update($data);

        return redirect()
            ->route('it.emplacements.index')
            ->with('success', "L'emplacement « {$emplacement->nom} » a été mis à jour.");
    }

    /**
     * Toggle status between active and archived.
     */
    public function toggleStatus(Request $request, Emplacement $emplacement): RedirectResponse
    {
        $this->ensureTablesExist();

        $newStatus = $emplacement->isActif() ? Emplacement::STATUT_ARCHIVE : Emplacement::STATUT_ACTIF;
        $label = $newStatus === Emplacement::STATUT_ACTIF ? 'réactivé' : 'archivé';

        $emplacement->update(['statut' => $newStatus]);

        return back()->with('success', "L'emplacement « {$emplacement->nom} » a été {$label} avec succès.");
    }

    /**
     * Delete an empty location.
     */
    public function destroy(Emplacement $emplacement): RedirectResponse
    {
        $this->ensureTablesExist();

        $hasEquipements = $emplacement->equipements()->count() > 0;
        $hasStock = $emplacement->stocks()->where('quantite', '>', 0)->count() > 0;

        if ($hasEquipements || $hasStock) {
            return back()->with('error', "Impossible de supprimer l'emplacement « {$emplacement->nom} » car il contient encore du matériel ou des stocks physiques. Veuillez d'abord transférer ces éléments ou archiver l'emplacement.");
        }

        $nom = $emplacement->nom;
        $emplacement->delete();

        return redirect()
            ->route('it.emplacements.index')
            ->with('success', "L'emplacement « {$nom} » a été supprimé avec succès.");
    }
}
