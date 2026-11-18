<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
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
     * Inventaire des produits, catégories, En stock, En utilisation, En panne, Total, emplacements.
     */
    public function inventaire(Request $request): View|Response|JsonResponse
    {
        $search = $request->string('search')->trim()->value();
        $categoryId = $request->input('category_id');
        $situation = $request->string('situation')->trim()->value();
        $stockAlert = $request->string('stock_alert')->trim()->value();

        $query = Produit::query()
            ->with(['categorie', 'stocks'])
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
            ->when($situation && $situation !== 'tous', function ($q) use ($situation) {
                $q->enSituation($situation);
            });

        if ($stockAlert === 'rupture') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) = 0');
        } elseif ($stockAlert === 'faible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) > 0')
                  ->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) <= COALESCE(NULLIF(produits.seuil_minimum, 0), 1)');
        } elseif ($stockAlert === 'normal' || $stockAlert === 'disponible') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantite), 0) FROM stocks WHERE stocks.produit_id = produits.id AND (stocks.situation = "en_stock" OR stocks.situation IS NULL)) > COALESCE(NULLIF(produits.seuil_minimum, 0), 1)');
        }

        // Export Excel (.csv compatible Excel avec UTF-8 BOM)
        if ($request->input('export') === 'excel') {
            $items = $query->orderBy('nom')->get();
            $headers = ['Matériel', 'Catégorie', 'En stock', 'En utilisation', 'En panne', 'Total'];
            $rows = $items->map(function ($p) {
                return [
                    $p->nom ?? $p->designation ?? '-',
                    $p->categorie->nom ?? 'Non classé',
                    $p->quantite_en_stock,
                    $p->quantite_en_utilisation,
                    $p->quantite_en_panne,
                    $p->total_stock,
                ];
            });

            return $this->exportService->streamExcelCsv(
                'rapport_stock_' . now()->format('Ymd_His') . '.csv',
                $headers,
                $rows,
                ['title' => 'Rapport de stock', 'filters' => $this->buildFilterSummary($request)]
            );
        }

        // Export PDF
        if ($request->input('export') === 'pdf') {
            $items = $query->orderBy('nom')->get();
            $headers = ['Matériel', 'Catégorie', 'En stock', 'En utilisation', 'En panne', 'Total'];
            $rows = $items->map(function ($p) {
                return [
                    $p->nom ?? $p->designation ?? '-',
                    $p->categorie->nom ?? 'Non classé',
                    $p->quantite_en_stock,
                    $p->quantite_en_utilisation,
                    $p->quantite_en_panne,
                    $p->total_stock,
                ];
            });

            $html = $this->exportService->renderPdfHtml(
                'Rapport de Stock — Stock Matériel',
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
            $itemsAll = (clone $query)->get();
            $totalEnStock = $itemsAll->sum('quantite_en_stock');
            $totalEnUtil = $itemsAll->sum('quantite_en_utilisation');
            $totalEnPanne = $itemsAll->sum('quantite_en_panne');
            $totalUnites = $itemsAll->sum('total_stock');

            $senderUser = auth()->user();
            $senderNom = $senderUser?->name ?? 'Gestionnaire de stock';
            $senderEmail = $senderUser?->email ?? config('mail.from.address', '');

            $sujet = 'Rapport de stock matériel — ' . now()->format('d/m/Y');
            $corpsLignes = [
                'Bonjour ' . $destinataireNom . ',',
                '',
                'Veuillez trouver ci-joint le rapport de stock de matériel informatique édité le ' . now()->format('d/m/Y à H:i') . '.',
                '',
                '■ SYNTHÈSE DU STOCK :',
                '- Matériels enregistrés : ' . $totalArticles,
                '- En stock : ' . $totalEnStock,
                '- En utilisation : ' . $totalEnUtil,
                '- En panne : ' . $totalEnPanne,
                '- Total unités : ' . $totalUnites,
            ];

            $filterSummary = $this->buildFilterSummary($request);
            if (!empty($filterSummary)) {
                $corpsLignes[] = '- Filtres appliqués : ' . $filterSummary;
            }

            $corpsLignes[] = '';
            $corpsLignes[] = 'Cordialement,';
            $corpsLignes[] = $senderNom;

            $corps = implode("\r\n", $corpsLignes);
            $gmailUrl = $this->gmailService->buildGmailUrl($destinataireEmail, $sujet, $corps);

            return response()->json([
                'success' => true,
                'gmail_url' => $gmailUrl,
                'destinataire' => $destinataireEmail,
                'pdf_url' => route('it.reports.inventaire', array_merge($request->except(['action']), ['export' => 'pdf'])),
            ]);
        }

        $materiels = $query->orderBy('nom')->paginate(10)->withQueryString();
        $categories = Categorie::where('statut', Categorie::STATUT_ACTIF)->orderBy('nom')->get();
        $emplacements = Emplacement::where('statut', Emplacement::STATUT_ACTIF)->orderBy('nom')->get();
        $destinataireConfig = $this->gmailService->getDirecteurConfig();

        return view('it.reports.inventaire', compact('materiels', 'categories', 'emplacements', 'destinataireConfig'));
    }

    /**
     * Rapport 2 — Rapport des mouvements
     * Entrées, sorties, transferts, changements de situation et corrections avec dates, situations, quantités.
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
            if ($type !== 'tous') {
                $query->where('type', $type);
            }
        }

        if ($situationDepart = $request->string('situation_depart')->trim()->value()) {
            if ($situationDepart !== 'tous') {
                $query->where('situation_depart', $situationDepart);
            }
        }

        if ($situationArrivee = $request->string('situation_arrivee')->trim()->value()) {
            if ($situationArrivee !== 'tous') {
                $query->where('situation_arrivee', $situationArrivee);
            }
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
            $headers = ['Date', 'Matériel', 'Opération', 'Situation départ', 'Situation arrivée', 'Quantité', 'Utilisateur', 'Motif'];
            $rows = $items->map(function ($m) {
                return [
                    $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-',
                    $m->produit->nom ?? $m->produit->designation ?? '-',
                    $m->type_label ?? ucfirst($m->type),
                    $m->situation_depart_label ?? '-',
                    $m->situation_arrivee_label ?? '-',
                    $m->quantite,
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
            $headers = ['Date', 'Matériel', 'Opération', 'Situation départ', 'Situation arrivée', 'Quantité', 'Utilisateur', 'Motif'];
            $rows = $items->map(function ($m) {
                return [
                    $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-',
                    $m->produit->nom ?? $m->produit->designation ?? '-',
                    $m->type_label ?? ucfirst($m->type),
                    $m->situation_depart_label ?? '-',
                    $m->situation_arrivee_label ?? '-',
                    $m->quantite . ' unité(s)',
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

            $senderUser = auth()->user();
            $senderNom = $senderUser?->name ?? 'Gestionnaire de stock';
            $senderEmail = $senderUser?->email ?? config('mail.from.address', '');

            $sujet = 'Rapport des mouvements de stock — ' . now()->format('d/m/Y');
            $corpsLignes = [
                'Bonjour ' . $destinataireNom . ',',
                '',
                'Veuillez trouver ci-joint le relevé des mouvements de stock édité le ' . now()->format('d/m/Y à H:i') . '.',
                '',
                '■ SYNTHÈSE DES MOUVEMENTS :',
                '- Nombre de mouvements : ' . $totalMouvements,
            ];

            $filterSummary = $this->buildFilterSummary($request);
            if (!empty($filterSummary)) {
                $corpsLignes[] = '- Filtres appliqués : ' . $filterSummary;
            }

            $corpsLignes[] = '';
            $corpsLignes[] = 'Cordialement,';
            $corpsLignes[] = $senderNom;

            $corps = implode("\r\n", $corpsLignes);
            $gmailUrl = $this->gmailService->buildGmailUrl($destinataireEmail, $sujet, $corps);

            return response()->json([
                'success' => true,
                'gmail_url' => $gmailUrl,
                'destinataire' => $destinataireEmail,
                'pdf_url' => route('it.reports.mouvements', array_merge($request->except(['action']), ['export' => 'pdf'])),
            ]);
        }

        $mouvements = $query->latest('date_mouvement')->latest('id')->paginate(10)->withQueryString();
        $typesMouvements = Mouvement::TYPES;
        $destinataireConfig = $this->gmailService->getDirecteurConfig();

        return view('it.reports.mouvements', compact('mouvements', 'typesMouvements', 'destinataireConfig'));
    }

    /**
     * Helper pour résumer les filtres dans le titre du PDF ou l'e-mail.
     */
    protected function buildFilterSummary(Request $request): string
    {
        $parts = [];
        if ($search = $request->input('search')) {
            $parts[] = "Recherche: \"{$search}\"";
        }
        if ($catId = $request->input('category_id')) {
            $cat = Categorie::find($catId);
            if ($cat) {
                $parts[] = "Catégorie: {$cat->nom}";
            }
        }
        if ($situation = $request->input('situation')) {
            $parts[] = "Situation: " . (Stock::SITUATIONS[$situation] ?? $situation);
        }
        if ($alert = $request->input('stock_alert')) {
            $parts[] = "Niveau: " . match ($alert) {
                'rupture' => 'Épuisé',
                'faible' => 'Stock faible',
                default => 'Disponible',
            };
        }
        if ($type = $request->input('type')) {
            $parts[] = "Opération: " . (Mouvement::TYPES[$type] ?? ucfirst($type));
        }
        if ($debut = $request->input('date_debut')) {
            $parts[] = "Du: {$debut}";
        }
        if ($fin = $request->input('date_fin')) {
            $parts[] = "Au: {$fin}";
        }

        return implode(', ', $parts);
    }
}
