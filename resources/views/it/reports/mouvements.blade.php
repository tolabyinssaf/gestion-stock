<x-layouts.dashboard title="Rapport des mouvements" :breadcrumbs="['Rapports & Exports' => route('it.reports.index'), 'Rapport des mouvements' => null]">

    <div x-data="rapportMouvementsView()">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="history" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Rapport des mouvements</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Entrées, sorties, transferts et corrections de stock avec dates, quantités et utilisateurs responsables.
                    </p>
                </div>
            </div>

            <!-- Actions : Exporter (Excel/PDF) & Envoyer (Gmail) -->
            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <!-- Exporter Excel -->
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors"
                    title="Télécharger le journal des mouvements en fichier Excel (.csv UTF-8)"
                >
                    <x-ui.icon name="file-spreadsheet" class="w-4 h-4" />
                    Exporter Excel
                </a>

                <!-- Exporter PDF -->
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                    title="Ouvrir le rapport PDF prêt à imprimer ou sauvegarder"
                >
                    <x-ui.icon name="printer" class="w-4 h-4" />
                    Imprimer / PDF
                </a>

                <!-- Envoyer par Gmail -->
                <button
                    type="button"
                    @click="openSendModal()"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-accent bg-accent hover:bg-accent/90 text-white text-xs font-bold shadow-xs transition-colors"
                    title="Générer le PDF filtré et préparer un courriel Gmail prérempli"
                >
                    <x-ui.icon name="send" class="w-4 h-4" />
                    Envoyer par Gmail
                </button>
            </div>
        </div>

        <!-- Filters Form (Consulter avec filtres adaptés) -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('it.reports.mouvements') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <!-- Search -->
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher produit, réf, motif..."
                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <!-- Type d'opération -->
                <div>
                    <select name="type" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous types d'opération</option>
                        <option value="entree" {{ request('type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie" {{ request('type') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                        <option value="transfert" {{ request('type') === 'transfert' ? 'selected' : '' }}>Transfert</option>
                        <option value="correction" {{ request('type') === 'correction' ? 'selected' : '' }}>Correction</option>
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20" title="Date de début">
                </div>

                <!-- Date fin -->
                <div>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20" title="Date de fin">
                </div>

                <!-- Submit & Reset -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-light transition-colors">
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'type', 'date_debut', 'date_fin']))
                        <a href="{{ route('it.reports.mouvements') }}" class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors" title="Réinitialiser">
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <x-ui.table
            :headers="['Date', 'Produit', 'Type d\'opération', 'Quantité', 'Emplacement concerné', 'Utilisateur', 'Motif']"
            :isEmpty="$mouvements->isEmpty()"
            emptyIcon="history"
            emptyTitle="Aucun mouvement trouvé"
            emptyDescription="Aucun mouvement de stock ne correspond aux filtres sélectionnés."
        >
            @foreach ($mouvements as $m)
                @php
                    $empLabel = match ($m->type) {
                        'entree' => $m->emplacementDestination->nom ?? 'Stock',
                        'sortie' => $m->emplacementSource->nom ?? 'Stock',
                        'transfert' => ($m->emplacementSource->nom ?? '?') . ' → ' . ($m->emplacementDestination->nom ?? '?'),
                        'correction' => $m->emplacementDestination->nom ?? ($m->emplacementSource->nom ?? 'Stock'),
                        default => '-',
                    };
                @endphp
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Date -->
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-content-secondary">
                        <span class="font-medium text-content">{{ $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-' }}</span>
                        <div class="text-[10px] font-mono text-content-secondary mt-0.5">{{ $m->reference }}</div>
                    </td>

                    <!-- Produit -->
                    <td class="px-5 py-3 text-xs">
                        <div class="font-bold text-content">{{ $m->produit->nom ?? $m->produit->designation ?? '-' }}</div>
                        <div class="text-[11px] font-mono text-content-secondary">Réf: {{ $m->produit->reference ?? '-' }}</div>
                    </td>

                    <!-- Type d'opération -->
                    <td class="px-5 py-3 whitespace-nowrap text-xs">
                        @if ($m->type === 'entree')
                            <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full text-[11px] border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Entrée
                            </span>
                        @elseif ($m->type === 'sortie')
                            <span class="inline-flex items-center gap-1 font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full text-[11px] border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Sortie
                            </span>
                        @elseif ($m->type === 'transfert')
                            <span class="inline-flex items-center gap-1 font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full text-[11px] border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Transfert
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 font-semibold text-accent bg-amber-50 px-2 py-0.5 rounded-full text-[11px] border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Correction
                            </span>
                        @endif
                    </td>

                    <!-- Quantité -->
                    <td class="px-5 py-3 whitespace-nowrap text-xs font-bold text-content">
                        @if ($m->type === 'entree')
                            <span class="text-emerald-700">+{{ $m->quantite }}</span>
                        @elseif ($m->type === 'sortie')
                            <span class="text-rose-700">-{{ $m->quantite }}</span>
                        @else
                            <span>{{ $m->quantite }}</span>
                        @endif
                        <span class="text-[11px] font-normal text-content-secondary">unité(s)</span>
                    </td>

                    <!-- Emplacement concerné -->
                    <td class="px-5 py-3 text-xs text-content-secondary">
                        <span class="font-medium text-content">{{ $empLabel }}</span>
                    </td>

                    <!-- Utilisateur -->
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-content">
                        {{ $m->user->name ?? 'Système' }}
                    </td>

                    <!-- Motif -->
                    <td class="px-5 py-3 text-xs text-content max-w-xs truncate" title="{{ $m->motif }}">
                        {{ $m->motif ?? '-' }}
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $mouvements->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>

        <!-- Modal d'explication de l'envoi Gmail -->
        <div
            x-show="showSendModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-2xs flex items-center justify-center p-4"
            x-transition
            style="display: none;"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-border">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="mail" class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-content">Transmission du rapport des mouvements</h4>
                        <p class="text-xs text-content-secondary mt-0.5">Destinataire : {{ $destinataireConfig['email'] ?? 'Non configuré' }}</p>
                    </div>
                </div>

                <div class="space-y-3 mb-6 text-xs text-content">
                    <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl text-amber-900 space-y-1.5">
                        <p class="font-bold flex items-center gap-1.5">
                            <x-ui.icon name="info" class="w-4 h-4 text-accent" />
                            Procédure d'envoi par Gmail
                        </p>
                        <ol class="list-decimal list-inside space-y-1 text-[11px] text-amber-800">
                            <li>Le <strong>rapport PDF filtré</strong> va s'ouvrir pour être téléchargé / enregistré sur votre poste.</li>
                            <li><strong>Gmail</strong> s'ouvrira automatiquement avec un message prérempli destiné à la Direction (<span class="font-mono">{{ $destinataireConfig['email'] ?? '' }}</span>).</li>
                            <li><strong>Joignez manuellement le PDF</strong> téléchargé dans Gmail avant de cliquer sur Envoyer.</li>
                        </ol>
                    </div>
                    <p class="text-content-secondary text-[11px] italic">
                        L'application ne prétend pas que le message ou le fichier a été envoyé automatiquement.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="showSendModal = false"
                        class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        @click="triggerSend()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-bold transition-colors shadow-xs"
                    >
                        <x-ui.icon name="external-link" class="w-4 h-4" />
                        Générer le PDF & Ouvrir Gmail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function rapportMouvementsView() {
            return {
                showSendModal: false,
                openSendModal() {
                    this.showSendModal = true;
                },
                triggerSend() {
                    this.showSendModal = false;

                    // 1. URL PDF avec les mêmes filtres que la page courante
                    const pdfUrl = '{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}';
                    window.open(pdfUrl, '_blank');

                    // 2. Appel JSON pour récupérer l'URL Gmail
                    const apiUrl = '{{ request()->fullUrlWithQuery(['action' => 'get_gmail_url']) }}';
                    fetch(apiUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.gmail_url) {
                            setTimeout(() => {
                                window.open(data.gmail_url, '_blank');
                            }, 500);
                        }
                    })
                    .catch(err => {
                        console.error('Erreur préparation Gmail :', err);
                    });
                }
            };
        }
    </script>

</x-layouts.dashboard>
