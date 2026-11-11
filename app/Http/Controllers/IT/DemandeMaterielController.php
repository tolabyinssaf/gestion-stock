<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Http\Requests\IT\DemandeMaterielRequest;
use App\Models\AppSetting;
use App\Models\DemandeMateriel;
use App\Models\Produit;
use App\Services\DemandeGmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DemandeMaterielController extends Controller
{
    protected DemandeGmailService $gmailService;
    protected \App\Services\ActivityLoggerService $activityLogger;

    public function __construct(DemandeGmailService $gmailService, \App\Services\ActivityLoggerService $activityLogger)
    {
        $this->gmailService = $gmailService;
        $this->activityLogger = $activityLogger;
    }

    /**
     * Ensure tables exist.
     */
    protected function ensureTablesExist(): void
    {
        if (!Schema::hasTable('demandes_materiel') || !Schema::hasTable('app_settings')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'AppSettingSeeder', '--force' => true]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }

    /**
     * Display the list of all material requests (historique).
     */
    public function index(Request $request): View
    {
        $this->ensureTablesExist();

        $query = DemandeMateriel::with(['produit', 'user'])
            ->when(!$request->boolean('show_archived'), function ($q) {
                $q->active();
            });

        // Filters
        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('produit_designation', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%");
            });
        }

        if ($priorite = $request->string('priorite')->trim()->value()) {
            $query->where('priorite', $priorite);
        }

        if ($statut = $request->string('statut_preparation')->trim()->value()) {
            $query->where('statut_preparation', $statut);
        }

        // 3 Cartes statistiques utiles (Total, Brouillons, Demandes à traiter)
        $totalDemandes = DemandeMateriel::active()->count();
        $brouillonsCount = DemandeMateriel::active()->brouillon()->count();
        $demandesATraiterCount = DemandeMateriel::active()->whereIn('statut_preparation', [
            DemandeMateriel::STATUT_PRETE,
            DemandeMateriel::STATUT_OUVERTURE_GMAIL,
        ])->count();
        $pretesCount = $demandesATraiterCount;

        $demandes = $query->latest()->paginate(15)->withQueryString();

        return view('it.demandes.index', compact(
            'demandes',
            'totalDemandes',
            'brouillonsCount',
            'demandesATraiterCount',
            'pretesCount'
        ));
    }

    /**
     * Show the create form for a new demand.
     */
    public function create(Request $request): View
    {
        $this->ensureTablesExist();

        $produits = Produit::with('categorie')
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $selectedProduit = null;
        if ($produitId = $request->input('produit_id')) {
            $selectedProduit = Produit::with(['category', 'stocks'])
                ->where('statut', Produit::STATUT_ACTIF)
                ->find($produitId);
        }

        $directeurConfig = $this->gmailService->getDirecteurConfig();
        $isConfigured = $this->gmailService->isConfigured();

        return view('it.demandes.create', compact(
            'produits',
            'selectedProduit',
            'directeurConfig',
            'isConfigured'
        ));
    }

    /**
     * Store the demand (draft or prepare for Gmail).
     */
    public function store(DemandeMaterielRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $produit = Produit::with(['category', 'stocks'])->findOrFail($validated['produit_id']);

        if (!$this->gmailService->isConfigured() && ($validated['action_type'] ?? '') === 'prepare_gmail') {
            return back()->withInput()->with('error', 'L\'adresse e-mail du directeur n\'est pas configurée. Demandez à l\'administrateur de la définir dans les paramètres.');
        }

        $directeurConfig = $this->gmailService->getDirecteurConfig();

        $objet = $this->gmailService->genererObjet($produit);
        $corps = $this->gmailService->genererCorps(
            $produit,
            (int) $validated['quantite'],
            $validated['priorite'],
            $validated['motif'],
            $validated['commentaire'] ?? null,
            $user
        );

        $statutPreparation = ($validated['action_type'] ?? 'save_draft') === 'prepare_gmail'
            ? DemandeMateriel::STATUT_PRETE
            : DemandeMateriel::STATUT_BROUILLON;

        $demande = DemandeMateriel::create([
            'reference' => DemandeMateriel::generateReference(),
            'produit_id' => $produit->id,
            'user_id' => $user->id,
            'quantite' => $validated['quantite'],
            'priorite' => $validated['priorite'],
            'motif' => $validated['motif'],
            'commentaire' => $validated['commentaire'] ?? null,
            'statut_preparation' => $statutPreparation,
            'produit_designation' => $produit->designation ?? $produit->nom,
            'produit_reference' => $produit->reference,
            'produit_categorie' => $produit->category->nom ?? 'Non classé',
            'stock_disponible_snapshot' => $produit->total_stock,
            'destinataire_nom' => $directeurConfig['nom'],
            'email_destinataire' => $directeurConfig['email'] ?? '',
            'objet_email' => $objet,
            'corps_email' => $corps,
        ]);

        $this->activityLogger->log(
            module: 'demandes',
            actionType: 'creation',
            description: "Préparation de la demande matériel {$demande->reference} ({$demande->quantite} x {$demande->produit_designation})",
            entityType: 'DemandeMateriel',
            entityId: $demande->id,
            entityName: $demande->reference,
            oldValues: null,
            newValues: ['quantite' => $demande->quantite, 'priorite' => $demande->priorite, 'statut' => $demande->statut_preparation]
        );

        if ($statutPreparation === DemandeMateriel::STATUT_PRETE) {
            return redirect()->route('it.demandes.show', $demande)
                ->with('success', 'Demande préparée avec succès. Cliquez sur "Ouvrir dans Gmail" pour envoyer le message.');
        }

        return redirect()->route('it.demandes.show', $demande)
            ->with('success', 'Brouillon de demande enregistré avec succès.');
    }

    /**
     * Display a single demand with Gmail link.
     */
    public function show(DemandeMateriel $demande): View
    {
        $demande->load(['produit.category', 'user']);

        $gmailUrl = null;
        if ($demande->email_destinataire) {
            $gmailUrl = $this->gmailService->buildGmailUrl(
                $demande->email_destinataire,
                $demande->objet_email,
                $demande->corps_email
            );
        }

        return view('it.demandes.show', compact('demande', 'gmailUrl'));
    }

    /**
     * Edit a draft demand.
     */
    public function edit(DemandeMateriel $demande): View|RedirectResponse
    {
        if ($demande->statut_preparation !== DemandeMateriel::STATUT_BROUILLON) {
            return redirect()->route('it.demandes.show', $demande)
                ->with('error', 'Seuls les brouillons peuvent être modifiés.');
        }

        $produits = Produit::with('categorie')
            ->where('statut', Produit::STATUT_ACTIF)
            ->orderBy('nom')
            ->get();

        $directeurConfig = $this->gmailService->getDirecteurConfig();
        $isConfigured = $this->gmailService->isConfigured();

        return view('it.demandes.edit', compact('demande', 'produits', 'directeurConfig', 'isConfigured'));
    }

    /**
     * Update a draft demand.
     */
    public function update(DemandeMaterielRequest $request, DemandeMateriel $demande): RedirectResponse
    {
        if ($demande->statut_preparation !== DemandeMateriel::STATUT_BROUILLON) {
            return redirect()->route('it.demandes.show', $demande)
                ->with('error', 'Seuls les brouillons peuvent être modifiés.');
        }

        $validated = $request->validated();
        $user = $request->user();
        $produit = Produit::with(['category', 'stocks'])->findOrFail($validated['produit_id']);

        $directeurConfig = $this->gmailService->getDirecteurConfig();

        $objet = $this->gmailService->genererObjet($produit);
        $corps = $this->gmailService->genererCorps(
            $produit,
            (int) $validated['quantite'],
            $validated['priorite'],
            $validated['motif'],
            $validated['commentaire'] ?? null,
            $user
        );

        $statutPreparation = ($validated['action_type'] ?? 'update') === 'prepare_gmail'
            ? DemandeMateriel::STATUT_PRETE
            : DemandeMateriel::STATUT_BROUILLON;

        $demande->update([
            'produit_id' => $produit->id,
            'quantite' => $validated['quantite'],
            'priorite' => $validated['priorite'],
            'motif' => $validated['motif'],
            'commentaire' => $validated['commentaire'] ?? null,
            'statut_preparation' => $statutPreparation,
            'produit_designation' => $produit->designation ?? $produit->nom,
            'produit_reference' => $produit->reference,
            'produit_categorie' => $produit->category->nom ?? 'Non classé',
            'stock_disponible_snapshot' => $produit->total_stock,
            'destinataire_nom' => $directeurConfig['nom'],
            'email_destinataire' => $directeurConfig['email'] ?? '',
            'objet_email' => $objet,
            'corps_email' => $corps,
        ]);

        return redirect()->route('it.demandes.show', $demande)
            ->with('success', 'Demande mise à jour avec succès.');
    }

    /**
     * Record that Gmail was opened (called via AJAX after window.open).
     */
    public function recordGmailOpened(DemandeMateriel $demande): JsonResponse
    {
        $demande->update([
            'statut_preparation' => DemandeMateriel::STATUT_OUVERTURE_GMAIL,
            'date_ouverture_gmail' => now(),
        ]);

        $this->activityLogger->log(
            module: 'demandes',
            actionType: 'ouverture_gmail',
            description: "Ouverture de l'interface Gmail pour la demande {$demande->reference}",
            entityType: 'DemandeMateriel',
            entityId: $demande->id,
            entityName: $demande->reference,
            oldValues: ['statut' => DemandeMateriel::STATUT_PRETE],
            newValues: ['statut' => DemandeMateriel::STATUT_OUVERTURE_GMAIL, 'date_ouverture_gmail' => now()->toDateTimeString()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ouverture Gmail enregistrée.',
            'statut_label' => $demande->fresh()->statut_preparation_label,
        ]);
    }

    /**
     * Duplicate a demand.
     */
    public function duplicate(DemandeMateriel $demande): RedirectResponse
    {
        $produit = Produit::with(['category', 'stocks'])->find($demande->produit_id);

        if (!$produit || $produit->statut !== Produit::STATUT_ACTIF) {
            return redirect()->route('it.demandes.index')
                ->with('error', 'Le produit de cette demande n\'est plus actif dans le catalogue.');
        }

        $directeurConfig = $this->gmailService->getDirecteurConfig();
        $user = auth()->user();

        $objet = $this->gmailService->genererObjet($produit);
        $corps = $this->gmailService->genererCorps(
            $produit,
            $demande->quantite,
            $demande->priorite,
            $demande->motif,
            $demande->commentaire,
            $user
        );

        $newDemande = DemandeMateriel::create([
            'reference' => DemandeMateriel::generateReference(),
            'produit_id' => $produit->id,
            'user_id' => $user->id,
            'quantite' => $demande->quantite,
            'priorite' => $demande->priorite,
            'motif' => $demande->motif,
            'commentaire' => $demande->commentaire,
            'statut_preparation' => DemandeMateriel::STATUT_BROUILLON,
            'produit_designation' => $produit->designation ?? $produit->nom,
            'produit_reference' => $produit->reference,
            'produit_categorie' => $produit->category->nom ?? 'Non classé',
            'stock_disponible_snapshot' => $produit->total_stock,
            'destinataire_nom' => $directeurConfig['nom'],
            'email_destinataire' => $directeurConfig['email'] ?? '',
            'objet_email' => $objet,
            'corps_email' => $corps,
        ]);

        return redirect()->route('it.demandes.show', $newDemande)
            ->with('success', 'Demande dupliquée avec succès en tant que nouveau brouillon.');
    }

    /**
     * Archive a demand.
     */
    public function archive(DemandeMateriel $demande): RedirectResponse
    {
        $demande->update(['est_archive' => true]);

        return redirect()->route('it.demandes.index')
            ->with('success', 'Demande archivée avec succès.');
    }

    /**
     * Mark a demand as sent (declarative action by the user).
     */
    public function markSent(DemandeMateriel $demande): RedirectResponse
    {
        $demande->update([
            'statut_preparation' => DemandeMateriel::STATUT_ENVOYEE,
        ]);

        $this->activityLogger->log(
            module: 'demandes',
            actionType: 'envoi_declare',
            description: "Demande {$demande->reference} marquée comme envoyée (déclaration utilisateur)",
            entityType: 'DemandeMateriel',
            entityId: $demande->id,
            entityName: $demande->reference
        );

        return back()->with('success', 'Demande marquée comme envoyée (suivi déclaratif).');
    }
}
