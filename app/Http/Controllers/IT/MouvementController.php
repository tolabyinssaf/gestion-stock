<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\Mouvement;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MouvementController extends Controller
{
    /**
     * Ensure database tables exist before querying.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('mouvements')) {
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
     * Display a listing of stock movements.
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $search = $request->string('search')->trim()->value();
        $type = $request->string('type')->trim()->value();
        $produitId = $request->input('produit_id');
        $equipementId = $request->input('equipement_id');
        $emplacementId = $request->input('emplacement_id');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        // KPIs
        $totalMouvements = Mouvement::count();
        $entreesCount = Mouvement::entrees()->count();
        $sortiesCount = Mouvement::sorties()->count();
        $transfertsCount = Mouvement::transferts()->count();
        $correctionsCount = Mouvement::corrections()->count();

        // Datasets for filters
        $produits = Produit::orderBy('nom')->get();
        $equipements = Equipement::orderBy('code_inventaire')->get();
        $emplacements = Emplacement::orderBy('nom')->get();

        // Query
        $mouvements = Mouvement::query()
            ->with(['produit.categorie', 'equipement', 'emplacementSource', 'emplacementDestination', 'user'])
            ->when($search, fn ($q) => $q->recherche($search))
            ->when($type, fn ($q) => $q->type($type))
            ->when($produitId, fn ($q) => $q->produit($produitId))
            ->when($equipementId, fn ($q) => $q->equipement($equipementId))
            ->when($emplacementId, function ($q) use ($emplacementId) {
                $q->where(fn ($sub) => $sub->where('emplacement_source_id', $emplacementId)->orWhere('emplacement_destination_id', $emplacementId));
            })
            ->when($dateDebut, fn ($q) => $q->whereDate('date_mouvement', '>=', $dateDebut))
            ->when($dateFin, fn ($q) => $q->whereDate('date_mouvement', '<=', $dateFin))
            ->orderBy('date_mouvement', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('it.mouvements.index', compact(
            'mouvements',
            'totalMouvements',
            'entreesCount',
            'sortiesCount',
            'transfertsCount',
            'correctionsCount',
            'produits',
            'equipements',
            'emplacements',
            'search',
            'type',
            'produitId',
            'equipementId',
            'emplacementId',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Display the specified movement voucher / receipt.
     */
    public function show(Mouvement $mouvement): View
    {
        $this->ensureTablesExist();

        $mouvement->load(['produit.categorie', 'equipement', 'emplacementSource', 'emplacementDestination', 'user']);

        return view('it.mouvements.show', compact('mouvement'));
    }
}
