<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Services\DemandeGmailService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    protected ExportService $exportService;
    protected DemandeGmailService $gmailService;

    public function __construct(ExportService $exportService, DemandeGmailService $gmailService)
    {
        $this->exportService = $exportService;
        $this->gmailService = $gmailService;
    }

    /**
     * Hub principal des Rapports & Exports.
     * Conserve uniquement les 2 rapports essentiels :
     * 1. Rapport de stock
     * 2. Rapport des mouvements
     */
    public function index(): View
    {
        $directeurConfig = $this->gmailService->getDirecteurConfig();

        return view('it.reports.index', [
            'destinataireEmail' => $directeurConfig['email'] ?? 'Non configuré',
            'destinataireNom' => $directeurConfig['nom'] ?? 'Direction Générale',
        ]);
    }

    /**
     * Rapport 1 — Rapport de stock
     * Inventaire des produits, catégories, quantités, emplacements et états du stock.
     * Actions : Consulter (HTML), Exporter (Excel/PDF), Envoyer (Téléchargement PDF + Ouverture Gmail).
     */
    public function inventaire(Request $request): View|Response|JsonResponse
    {
        $search = $request->string('search')->trim()->value();
        $categoryId = $request->input('category_id');
        $emplacementId = $request->input('emplacement_id');
        $stockAlert = $request->string('stock_alert')->trim()->value();

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
            ->when($categoryId, fn($q) => $q->where('categorie_id', $categoryId))
            ->when($emplacementId, function ($q) use ($emplacementId) {
                $q->whereHas('stocks', fn($s) => $s->where('emplacement_id', $emplacementId)->where('quantite', '>', 0));
            });

        if ($stockAlert === 'rupture') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id) = 0');
        } elseif ($stockAlert === 'faible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id) = 1');
        } elseif ($stockAlert === 'normal' || $stockAlert === 'disponible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id) > 1');
        }

        // Export Excel (.csv compatible Excel avec UTF-8 BOM)
        if ($request->input('export') === 'excel') {
            $items = $query->orderBy('nom')->get();
            $headers = ['Nom du matériel', 'Catégorie', 'Marque', 'Référence', 'Quantité totale', 'Emplacement(s)', 'État du stock'];
            $rows = $items->map(function ($p) {
                $emplacementsStr = $p->stocks->filter(fn($s) => $s->quantite > 0)
                    ->map(fn($s) => ($s->emplacement->nom ?? 'Inconnu') . ' (' . $s->quantite . ')')
                    ->implode(', ');
                if (empty($emplacementsStr)) {
                    $emplacementsStr = 'Aucun stock consigné';
                }
                return [
                    $p->nom ?? $p->designation ?? '-',
                    $p->categorie->nom ?? 'Non classé',
                    $p->marque ?? '-',
                    $p->reference ?? '-',
                    $p->total_stock,
                    $emplacementsStr,
                    $p->stock_status_label,
                ];
            });

            return $this->exportService->streamExcelCsv(
                'rapport_stock_' . now()->format('Ymd_His') . '.csv',
                $headers,
                $rows,
                ['title' => 'Rapport de stock — Inventaire du matériel informatique', 'filters' => $this->buildFilterSummary($request)]
            );
        }

        // Export PDF
        if ($request->input('export') === 'pdf') {
            $items = $query->orderBy('nom')->get();
            $headers = ['Nom du matériel', 'Catégorie', 'Marque / Réf.', 'Quantité', 'Emplacement(s)', 'État du stock'];
            $rows = $items->map(function ($p) {
                $emplacementsStr = $p->stocks->filter(fn($s) => $s->quantite > 0)
                    ->map(fn($s) => ($s->emplacement->nom ?? 'Inconnu') . ' (' . $s->quantite . ')')
                    ->implode(', ');
                if (empty($emplacementsStr)) {
                    $emplacementsStr = 'Aucun stock consigné';
                }
                return [
                    $p->nom ?? $p->designation ?? '-',
                    $p->categorie->nom ?? 'Non classé',
                    ($p->marque ? $p->marque . ' — ' : '') . ($p->reference ?? '-'),
                    $p->total_stock . ' unité(s)',
                    $emplacementsStr,
                    $p->stock_status_label,
                ];
            });

            $html = $this->exportService->renderPdfHtml(
                'Rapport de Stock — Inventaire du Matériel Informatique',
                $headers,
                $rows,
                ['filters' => $this->buildFilterSummary($request)],
                'landscape'
            );

            return response($html);
        }

        // Action Envoyer par Gmail (Renvoie l'URL Gmail préremplie)
        if ($request->wantsJson() && $request->input('action') === 'get_gmail_url') {
            $destinataireConfig = $this->gmailService->getDirecteurConfig();
            $destinataireEmail = $destinataireConfig['email'] ?? config('mail.from.address', '');
            $destinataireNom = $destinataireConfig['nom'] ?? 'Direction Générale';

            $totalArticles = (clone $query)->count();
            $totalUnites = (int) (clone $query)->get()->sum('total_stock');

            $sujet = 'Rapport de stock informatique — ' . now()->format('d/m/Y');
            $corpsLignes = [
                'Bonjour ' . $destinataireNom . ',',
                '',
                'Veuillez trouver ci-joint le rapport de stock de matériel informatique édité le ' . now()->format('d/m/Y à H:i') . '.',
                '',
                'Synthèse des données filtrées :',
                '- Total références répertoriées : ' . $totalArticles,
                '- Total unités disponibles : ' . $totalUnites,
            ];

            $filterSummary = $this->buildFilterSummary($request);
            if (!empty($filterSummary)) {
                $corpsLignes[] = '- Filtres appliqués : ' . $filterSummary;
            }

            $corpsLignes[] = '';
            $corpsLignes[] = 'Le fichier PDF correspondant a été généré depuis IT Manager Pro. Pensez à l\'insérer en pièce jointe de ce courriel avant l\'envoi.';
            $corpsLignes[] = '';
            $corpsLignes[] = 'Cordialement,';
            $corpsLignes[] = auth()->user()->name ?? 'Responsable IT';
            $corpsLignes[] = 'Service informatique — IT Manager Pro';

            $corps = implode("\r\n", $corpsLignes);
            $gmailUrl = $this->gmailService->buildGmailUrl($destinataireEmail, $sujet, $corps);

            return response()->json([
                'success' => true,
                'gmail_url' => $gmailUrl,
                'destinataire' => $destinataireEmail,
                'pdf_url' => route('it.reports.inventaire', array_merge($request->except(['action']), ['export' => 'pdf'])),
            ]);
        }

        $materiels = $query->orderBy('nom')->paginate(20)->withQueryString();
        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)->orderBy('nom')->get();
        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)->orderBy('nom')->get();
        $destinataireConfig = $this->gmailService->getDirecteurConfig();

        return view('it.reports.inventaire', compact('materiels', 'categories', 'emplacements', 'destinataireConfig'));
    }

    /**
     * Rapport 2 — Rapport des mouvements
     * Entrées, sorties, transferts et corrections avec dates, quantités et utilisateurs.
     * Actions : Consulter (HTML), Exporter (Excel/PDF), Envoyer (Téléchargement PDF + Ouverture Gmail).
     */
    public function mouvements(Request $request): View|Response|JsonResponse
    {
        $query = Mouvement::with(['produit', 'user', 'emplacementSource', 'emplacementDestination']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhere('beneficiaire', 'like', "%{$search}%")
                  ->orWhereHas('produit', fn($pq) => $pq->where('nom', 'like', "%{$search}%")->orWhere('reference', 'like', "%{$search}%"));
            });
        }

        if ($type = $request->string('type')->trim()->value()) {
            $query->where('type', $type);
        }

        if ($dateDebut = $request->input('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $dateDebut);
        }

        if ($dateFin = $request->input('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $dateFin);
        }

        // Export Excel (.csv compatible Excel avec UTF-8 BOM)
        if ($request->input('export') === 'excel') {
            $items = $query->latest('date_mouvement')->get();
            $headers = ['Date', 'Produit', 'Type d\'opération', 'Quantité', 'Emplacement concerné', 'Utilisateur', 'Motif'];
            $rows = $items->map(function ($m) {
                $emplacementLabel = match ($m->type) {
                    'entree' => $m->emplacementDestination->nom ?? 'Stock',
                    'sortie' => $m->emplacementSource->nom ?? 'Stock',
                    'transfert' => ($m->emplacementSource->nom ?? '?') . ' -> ' . ($m->emplacementDestination->nom ?? '?'),
                    'correction' => $m->emplacementDestination->nom ?? ($m->emplacementSource->nom ?? 'Stock'),
                    default => '-',
                };
                return [
                    $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-',
                    $m->produit->nom ?? $m->produit->designation ?? '-',
                    ucfirst($m->type),
                    $m->quantite,
                    $emplacementLabel,
                    $m->user->name ?? 'Système',
                    $m->motif ?? '-',
                ];
            });

            return $this->exportService->streamExcelCsv(
                'rapport_mouvements_' . now()->format('Ymd_His') . '.csv',
                $headers,
                $rows,
                ['title' => 'Rapport des mouvements de stock', 'filters' => $this->buildFilterSummary($request)]
            );
        }

        // Export PDF
        if ($request->input('export') === 'pdf') {
            $items = $query->latest('date_mouvement')->get();
            $headers = ['Date', 'Produit', 'Opération', 'Quantité', 'Emplacement concerné', 'Utilisateur', 'Motif'];
            $rows = $items->map(function ($m) {
                $emplacementLabel = match ($m->type) {
                    'entree' => $m->emplacementDestination->nom ?? 'Stock',
                    'sortie' => $m->emplacementSource->nom ?? 'Stock',
                    'transfert' => ($m->emplacementSource->nom ?? '?') . ' -> ' . ($m->emplacementDestination->nom ?? '?'),
                    'correction' => $m->emplacementDestination->nom ?? ($m->emplacementSource->nom ?? 'Stock'),
                    default => '-',
                };
                return [
                    $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-',
                    $m->produit->nom ?? $m->produit->designation ?? '-',
                    ucfirst($m->type),
                    $m->quantite . ' unité(s)',
                    $emplacementLabel,
                    $m->user->name ?? 'Système',
                    $m->motif ?? '-',
                ];
            });

            $html = $this->exportService->renderPdfHtml(
                'Rapport des Mouvements de Stock',
                $headers,
                $rows,
                ['filters' => $this->buildFilterSummary($request)],
                'landscape'
            );

            return response($html);
        }

        // Action Envoyer par Gmail (Renvoie l'URL Gmail préremplie)
        if ($request->wantsJson() && $request->input('action') === 'get_gmail_url') {
            $destinataireConfig = $this->gmailService->getDirecteurConfig();
            $destinataireEmail = $destinataireConfig['email'] ?? config('mail.from.address', '');
            $destinataireNom = $destinataireConfig['nom'] ?? 'Direction Générale';

            $totalMouvements = (clone $query)->count();

            $sujet = 'Rapport des mouvements de stock informatique — ' . now()->format('d/m/Y');
            $corpsLignes = [
                'Bonjour ' . $destinataireNom . ',',
                '',
                'Veuillez trouver ci-joint le journal des mouvements de stock informatique édité le ' . now()->format('d/m/Y à H:i') . '.',
                '',
                'Synthèse des opérations filtrées :',
                '- Nombre total d\'opérations enregistrées : ' . $totalMouvements,
            ];

            $filterSummary = $this->buildFilterSummary($request);
            if (!empty($filterSummary)) {
                $corpsLignes[] = '- Filtres appliqués : ' . $filterSummary;
            }

            $corpsLignes[] = '';
            $corpsLignes[] = 'Le fichier PDF correspondant a été généré depuis IT Manager Pro. Pensez à l\'insérer en pièce jointe de ce courriel avant l\'envoi.';
            $corpsLignes[] = '';
            $corpsLignes[] = 'Cordialement,';
            $corpsLignes[] = auth()->user()->name ?? 'Responsable IT';
            $corpsLignes[] = 'Service informatique — IT Manager Pro';

            $corps = implode("\r\n", $corpsLignes);
            $gmailUrl = $this->gmailService->buildGmailUrl($destinataireEmail, $sujet, $corps);

            return response()->json([
                'success' => true,
                'gmail_url' => $gmailUrl,
                'destinataire' => $destinataireEmail,
                'pdf_url' => route('it.reports.mouvements', array_merge($request->except(['action']), ['export' => 'pdf'])),
            ]);
        }

        $mouvements = $query->latest('date_mouvement')->paginate(20)->withQueryString();
        $destinataireConfig = $this->gmailService->getDirecteurConfig();

        return view('it.reports.mouvements', compact('mouvements', 'destinataireConfig'));
    }

    /**
     * Helper to summarize active filters for exports and emails.
     */
    protected function buildFilterSummary(Request $request): string
    {
        $active = [];
        foreach ($request->except(['page', 'export', 'action']) as $k => $v) {
            if (!empty($v)) {
                $label = match ($k) {
                    'search' => 'Recherche: "' . $v . '"',
                    'category_id' => 'Catégorie ID: ' . $v,
                    'emplacement_id' => 'Emplacement ID: ' . $v,
                    'stock_alert' => 'Alerte: ' . ucfirst($v),
                    'type' => 'Opération: ' . ucfirst($v),
                    'date_debut' => 'Du: ' . $v,
                    'date_fin' => 'Au: ' . $v,
                    default => $k . ': ' . $v,
                };
                $active[] = $label;
            }
        }

        return empty($active) ? 'Aucun filtre (ensemble des données)' : implode(', ', $active);
    }
}
