<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Http\Requests\IT\StoreEquipementRequest;
use App\Http\Requests\IT\UpdateEquipementRequest;
use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\EquipementActivite;
use App\Models\Produit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EquipementController extends Controller
{
    /**
     * Ensure database tables exist before querying.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('equipements') || !Schema::hasTable('emplacements')) {
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
     * Display a listing of physical IT equipments.
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        // Query parameters
        $search = $request->string('search')->trim()->value();
        $produitId = $request->input('produit_id');
        $categorieId = $request->input('categorie_id');
        $etatPhysique = $request->string('etat_physique')->trim()->value();
        $statutUtilisation = $request->string('statut_utilisation')->trim()->value();
        $emplacementId = $request->input('emplacement_id');
        $statut = $request->string('statut')->trim()->value();

        // KPIs calculation
        $totalEquipements = Equipement::count();
        $disponiblesCount = Equipement::disponible()->count();
        $enUtilisationCount = Equipement::enUtilisation()->count();
        $enPanneCount = Equipement::enPanne()->count();
        $horsServiceCount = Equipement::horsService()->count();

        // Filter dropdown datasets
        $produits = Produit::where('type_suivi', Produit::TYPE_INDIVIDUEL)->orderBy('nom')->get();
        $categories = Categorie::orderBy('nom')->get();
        $emplacements = Emplacement::orderBy('nom')->get();

        // Query with applied filters
        $equipements = Equipement::query()
            ->with(['produit.categorie', 'emplacement'])
            ->when($search, fn ($q) => $q->recherche($search))
            ->when($produitId, fn ($q) => $q->produit($produitId))
            ->when($categorieId, fn ($q) => $q->categorie($categorieId))
            ->when($etatPhysique, fn ($q) => $q->etatPhysique($etatPhysique))
            ->when($statutUtilisation, fn ($q) => $q->statutUtilisation($statutUtilisation))
            ->when($emplacementId, fn ($q) => $q->emplacement($emplacementId))
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('it.equipements.index', compact(
            'equipements',
            'produits',
            'categories',
            'emplacements',
            'totalEquipements',
            'disponiblesCount',
            'enUtilisationCount',
            'enPanneCount',
            'horsServiceCount',
            'search',
            'produitId',
            'categorieId',
            'etatPhysique',
            'statutUtilisation',
            'emplacementId',
            'statut'
        ));
    }

    /**
     * Show the form for creating a new equipment.
     */
    public function create(): View
    {
        $this->ensureTablesExist();

        // Selectable catalog products (Strictly individual/serialized tracking)
        $produits = Produit::with('categorie')
            ->where('type_suivi', Produit::TYPE_INDIVIDUEL)
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $suggestedCode = Equipement::generateCodeInventaire();

        return view('it.equipements.create', compact('produits', 'emplacements', 'suggestedCode'));
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(StoreEquipementRequest $request): RedirectResponse
    {
        $this->ensureTablesExist();

        $validated = $request->validated();
        $validated['created_by'] = auth()->id();
        $validated['numero_serie'] = !empty($validated['numero_serie']) ? trim($validated['numero_serie']) : null;
        $validated['statut'] = $validated['statut'] ?? Equipement::STATUT_ACTIF;

        $equipement = Equipement::create($validated);
        $equipement->load(['produit', 'emplacement']);

        // Log creation activity
        EquipementActivite::create([
            'equipement_id' => $equipement->id,
            'user_id' => auth()->id(),
            'action' => 'creation',
            'description' => "Enregistrement initial de l'équipement dans le parc informatique.",
            'nouvelles_valeurs' => [
                'code_inventaire' => $equipement->code_inventaire,
                'produit' => $equipement->produit->nom ?? '',
                'numero_serie' => $equipement->numero_serie ?: 'Non renseigné',
                'etat_physique' => $equipement->etat_physique_label,
                'statut_utilisation' => $equipement->statut_utilisation_label,
                'emplacement' => $equipement->emplacement->nom ?? 'Non assigné',
            ],
        ]);

        return redirect()
            ->route('it.equipements.show', $equipement)
            ->with('success', "L'équipement « {$equipement->code_inventaire} » a été référencé avec succès.");
    }

    /**
     * Display the specified equipment details.
     */
    public function show(Equipement $equipement): View
    {
        $this->ensureTablesExist();

        $equipement->load(['produit.categorie', 'emplacement', 'createur', 'modificateur', 'activites.user']);

        return view('it.equipements.show', compact('equipement'));
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipement $equipement): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with('categorie')
            ->where('type_suivi', Produit::TYPE_INDIVIDUEL)
            ->orderBy('nom')
            ->get();

        $emplacements = Emplacement::orderBy('nom')->get();

        return view('it.equipements.edit', compact('equipement', 'produits', 'emplacements'));
    }

    /**
     * Update the specified equipment in storage with activity logging.
     */
    public function update(UpdateEquipementRequest $request, Equipement $equipement): RedirectResponse
    {
        $this->ensureTablesExist();

        $oldValues = [
            'code_inventaire' => $equipement->code_inventaire,
            'numero_serie' => $equipement->numero_serie ?: 'Non renseigné',
            'etat_physique' => $equipement->etat_physique_label,
            'statut_utilisation' => $equipement->statut_utilisation_label,
            'emplacement' => $equipement->emplacement->nom ?? 'Non assigné',
        ];

        $validated = $request->validated();
        $validated['updated_by'] = auth()->id();
        $validated['numero_serie'] = !empty($validated['numero_serie']) ? trim($validated['numero_serie']) : null;

        $equipement->update($validated);
        $equipement->refresh()->load(['produit', 'emplacement']);

        $newValues = [
            'code_inventaire' => $equipement->code_inventaire,
            'numero_serie' => $equipement->numero_serie ?: 'Non renseigné',
            'etat_physique' => $equipement->etat_physique_label,
            'statut_utilisation' => $equipement->statut_utilisation_label,
            'emplacement' => $equipement->emplacement->nom ?? 'Non assigné',
        ];

        // Audit trail: Log changes
        EquipementActivite::create([
            'equipement_id' => $equipement->id,
            'user_id' => auth()->id(),
            'action' => 'modification',
            'description' => "Mise à jour des informations de l'équipement.",
            'anciennes_valeurs' => $oldValues,
            'nouvelles_valeurs' => $newValues,
        ]);

        return redirect()
            ->route('it.equipements.show', $equipement)
            ->with('success', "La fiche de l'équipement « {$equipement->code_inventaire} » a été mise à jour.");
    }

    /**
     * Toggle status between active and archived.
     */
    public function toggleStatus(Request $request, Equipement $equipement): RedirectResponse
    {
        $this->ensureTablesExist();

        $newStatus = $equipement->isActif() ? Equipement::STATUT_ARCHIVE : Equipement::STATUT_ACTIF;
        $actionName = $newStatus === Equipement::STATUT_ACTIF ? 'reactivation' : 'archivage';
        $actionLabel = $newStatus === Equipement::STATUT_ACTIF ? 'réactivé' : 'archivé';

        $equipement->update(['statut' => $newStatus]);

        EquipementActivite::create([
            'equipement_id' => $equipement->id,
            'user_id' => auth()->id(),
            'action' => $actionName,
            'description' => "L'équipement a été {$actionLabel}.",
            'nouvelles_valeurs' => ['statut' => $newStatus],
        ]);

        return back()->with('success', "L'équipement « {$equipement->code_inventaire} » a été {$actionLabel} avec succès.");
    }

    /**
     * Remove the specified equipment from storage if no historical activity exists.
     */
    public function destroy(Equipement $equipement): RedirectResponse
    {
        $this->ensureTablesExist();

        // Business rule: Prevent accidental loss of audit history
        if ($equipement->activites()->count() > 1) {
            return back()->with('error', "Impossible de supprimer définitivement cet équipement car il possède un historique d'activité. Utilisez plutôt l'option d'archivage.");
        }

        $code = $equipement->code_inventaire;
        $equipement->delete();

        return redirect()
            ->route('it.equipements.index')
            ->with('success', "L'équipement « {$code} » a été supprimé du parc.");
    }
}
