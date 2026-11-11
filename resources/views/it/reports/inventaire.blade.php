<x-layouts.dashboard title="Rapport de stock" :breadcrumbs="['Rapports & Exports' => route('it.reports.index'), 'Rapport de stock' => null]">

    <div x-data="rapportStockView()">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Rapport de stock</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Inventaire des produits, catégories, quantités disponibles, emplacements et états du stock.
                    </p>
                </div>
            </div>

            <!-- Actions : Exporter (Excel/PDF) & Envoyer (Gmail) -->
            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <!-- Exporter Excel -->
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors"
                    title="Télécharger un classeur Excel compatible (.csv UTF-8)"
                >
                    <x-ui.icon name="file-spreadsheet" class="w-4 h-4" />
                    Exporter Excel
                </a>

                <!-- Exporter PDF -->
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                    title="Ouvrir le document PDF prêt à être imprimé ou sauvegardé"
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
            <form method="GET" action="{{ route('it.reports.inventaire') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <!-- Search -->
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher nom, référence, marque..."
                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <!-- Catégorie -->
                <div>
                    <select name="category_id" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Emplacement -->
                <div>
                    <select name="emplacement_id" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous emplacements</option>
                        @foreach ($emplacements as $emp)
                            <option value="{{ $emp->id }}" {{ request('emplacement_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- État du stock -->
                <div>
                    <select name="stock_alert" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous les états</option>
                        <option value="rupture" {{ request('stock_alert') === 'rupture' ? 'selected' : '' }}>Rupture (= 0)</option>
                        <option value="faible" {{ request('stock_alert') === 'faible' ? 'selected' : '' }}>Stock faible (= 1)</option>
                        <option value="normal" {{ request('stock_alert') === 'normal' ? 'selected' : '' }}>Disponible (&gt; 1)</option>
                    </select>
                </div>

                <!-- Submit & Reset -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-light transition-colors">
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'category_id', 'emplacement_id', 'stock_alert']))
                        <a href="{{ route('it.reports.inventaire') }}" class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors" title="Réinitialiser">
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <x-ui.table
            :headers="['Nom du matériel', 'Catégorie', 'Marque / Réf.', 'Quantité totale', 'Emplacement(s)', 'État du stock']"
            :isEmpty="$materiels->isEmpty()"
            emptyIcon="boxes"
            emptyTitle="Aucun matériel correspondant"
            emptyDescription="Modifiez vos critères de recherche pour afficher les articles répertoriés."
        >
            @foreach ($materiels as $m)
                @php
                    $total = $m->total_stock;
                    $status = $m->stock_status;
                @endphp
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Nom du matériel -->
                    <td class="px-5 py-3">
                        <div class="text-xs font-bold text-content">{{ $m->nom ?? $m->designation ?? 'N/A' }}</div>
                        @if($m->modele)
                            <div class="text-[11px] text-content-secondary">{{ $m->modele }}</div>
                        @endif
                    </td>

                    <!-- Catégorie -->
                    <td class="px-5 py-3 text-xs text-content-secondary">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-surface text-content-secondary border border-border">
                            {{ $m->categorie->nom ?? 'Non classé' }}
                        </span>
                    </td>

                    <!-- Marque / Réf -->
                    <td class="px-5 py-3 text-xs">
                        <div class="font-mono text-xs font-semibold text-content">{{ $m->reference ?? '-' }}</div>
                        @if ($m->marque)
                            <div class="text-[11px] text-content-secondary">{{ $m->marque }}</div>
                        @endif
                    </td>

                    <!-- Quantité totale -->
                    <td class="px-5 py-3 text-xs font-bold {{ $total === 0 ? 'text-rose-600' : ($total === 1 ? 'text-accent' : 'text-content') }}">
                        {{ $total }} unité(s)
                    </td>

                    <!-- Emplacement(s) -->
                    <td class="px-5 py-3 text-xs">
                        @if($m->stocks->where('quantite', '>', 0)->isEmpty())
                            <span class="text-content-secondary italic text-[11px]">Aucun stock consigné</span>
                        @else
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @foreach($m->stocks->where('quantite', '>', 0) as $stockLine)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] bg-blue-50 border border-blue-200 text-primary">
                                        <span>{{ $stockLine->emplacement->nom ?? 'Inconnu' }} :</span>
                                        <strong>{{ $stockLine->quantite }}</strong>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </td>

                    <!-- État du stock -->
                    <td class="px-5 py-3 text-xs">
                        @if ($total === 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Rupture
                            </span>
                        @elseif ($total === 1)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Stock faible
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Disponible
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $materiels->links() }}
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
                        <h4 class="text-base font-bold text-content">Transmission du rapport de stock</h4>
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
        function rapportStockView() {
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
