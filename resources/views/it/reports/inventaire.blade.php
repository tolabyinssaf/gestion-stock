@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'boxes'            => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'file-spreadsheet' => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M8 13h2|M14 13h2|M8 17h2|M14 17h2',
        'printer'          => 'M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2|M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6|M8 14h8v7a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1z',
        'send'             => 'M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z|m21.854 2.147-10.94 10.939',
        'mail'             => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'info'             => 'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0z|M12 16v-4|M12 8h.01',
        'external-link'    => 'M15 3h6v6|M10 14 21 3|M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6',
        'x'                => 'M18 6 6 18|m6 6 12 12',
        'search'           => 'm21 21-4.34-4.34|M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16z',
        'filter'           => 'M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? $icons['boxes']) as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Rapport de stock" :breadcrumbs="['Rapports & Exports' => route('it.reports.index'), 'Rapport de stock' => null]">

    <div x-data="rapportStockView()">

        <!-- En-tête -->
        <div class="mb-6 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('boxes', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-content tracking-tight">Rapport de stock</h1>
                    <p class="text-xs sm:text-sm text-content-secondary mt-0.5">
                        Inventaire des produits, catégories, quantités disponibles, emplacements et états du stock.
                    </p>
                </div>
            </div>

            <!-- Actions : Exporter (Excel/PDF) & Envoyer (Gmail) -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 flex-wrap">
                <!-- Exporter Excel -->
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-xs font-semibold transition-colors"
                    title="Télécharger un classeur Excel compatible (.csv UTF-8)"
                >
                    {!! $svg('file-spreadsheet', 'w-4 h-4') !!}
                    Exporter Excel
                </a>

                <!-- Exporter PDF -->
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] hover:text-[#0092FD] transition-all"
                    title="Ouvrir le document PDF prêt à être imprimé ou sauvegardé"
                >
                    {!! $svg('printer', 'w-4 h-4') !!}
                    Imprimer / PDF
                </a>

                <!-- Envoyer par Gmail -->
                <button
                    type="button"
                    @click="openSendModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors cursor-pointer"
                    title="Générer le PDF filtré et préparer un courriel Gmail prérempli"
                >
                    {!! $svg('send', 'w-4 h-4') !!}
                    Envoyer par Gmail
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 mb-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <form method="GET" action="{{ route('it.reports.inventaire') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-secondary">
                        {!! $svg('search', 'w-4 h-4') !!}
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher nom, référence, marque..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                </div>

                <div>
                    <select name="category_id" class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all">
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="stock_alert" class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all">
                        <option value="">Tous les états</option>
                        <option value="rupture" {{ request('stock_alert') === 'rupture' ? 'selected' : '' }}>Rupture (= 0)</option>
                        <option value="faible" {{ request('stock_alert') === 'faible' ? 'selected' : '' }}>Stock faible (= 1)</option>
                        <option value="normal" {{ request('stock_alert') === 'normal' ? 'selected' : '' }}>Disponible (&gt; 1)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white rounded-full text-xs font-semibold transition-colors cursor-pointer"
                    >
                        {!! $svg('filter', 'w-4 h-4') !!}
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'category_id', 'stock_alert']))
                        <a
                            href="{{ route('it.reports.inventaire') }}"
                            class="p-2.5 bg-surface hover:bg-slate-100 text-content-secondary rounded-full transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            {!! $svg('x', 'w-4 h-4') !!}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste du stock -->
        <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] overflow-hidden mb-8">
            @if ($materiels->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        {!! $svg('boxes', 'w-8 h-8') !!}
                    </div>
                    <h3 class="text-base font-bold text-content">Aucun matériel correspondant</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">Modifiez vos critères de recherche pour afficher les articles répertoriés.</p>
                </div>
            @else
                <!-- Mobile / tablette : cartes -->
                <div class="lg:hidden p-4 space-y-3">
                    @foreach ($materiels as $m)
                        @php
                            $enStock = $m->quantite_en_stock;
                            $enUtil = $m->quantite_en_utilisation;
                            $enPanne = $m->quantite_en_panne;
                            $total = $m->total_stock;
                        @endphp
                        <div class="p-4 rounded-2xl bg-surface/60">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-content break-words">{{ $m->nom ?? $m->designation ?? 'N/A' }}</p>
                                <div class="flex items-center gap-2 flex-wrap text-[11px] text-content-secondary mt-0.5">
                                    <span class="font-mono">Réf : {{ $m->reference ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $m->categorie->nom ?? 'Non classé' }}</span>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-border/60 grid grid-cols-4 gap-2 text-center">
                                <div>
                                    <p class="text-[10px] text-content-secondary mb-1">En stock</p>
                                    <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-full font-bold text-xs {{ $enStock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-white text-content-secondary' }}">{{ $enStock }}</span>
                                </div>
                                <div>
                                    <p class="text-[10px] text-content-secondary mb-1">Utilisation</p>
                                    <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-full font-bold text-xs {{ $enUtil > 0 ? 'bg-blue-50 text-primary' : 'bg-white text-content-secondary' }}">{{ $enUtil }}</span>
                                </div>
                                <div>
                                    <p class="text-[10px] text-content-secondary mb-1">En panne</p>
                                    <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-full font-bold text-xs {{ $enPanne > 0 ? 'bg-rose-50 text-rose-700' : 'bg-white text-content-secondary' }}">{{ $enPanne }}</span>
                                </div>
                                <div>
                                    <p class="text-[10px] text-content-secondary mb-1">Total</p>
                                    <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 font-extrabold text-sm text-content">{{ $total }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PC : tableau -->
                <div class="hidden lg:block overflow-x-auto p-4">
                    <table class="w-full text-left text-xs border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-surface text-[11px] font-semibold text-content">
                                <th class="py-3 px-5 rounded-l-xl">Matériel</th>
                                <th class="py-3 px-4">Catégorie</th>
                                <th class="py-3 px-4 text-center">En stock</th>
                                <th class="py-3 px-4 text-center">En utilisation</th>
                                <th class="py-3 px-4 text-center">En panne</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($materiels as $m)
                                @php
                                    $enStock = $m->quantite_en_stock;
                                    $enUtil = $m->quantite_en_utilisation;
                                    $enPanne = $m->quantite_en_panne;
                                    $total = $m->total_stock;
                                @endphp
                                <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-content">{{ $m->nom ?? $m->designation ?? 'N/A' }}</div>
                                        <div class="text-[11px] text-content-secondary mt-0.5 font-mono">Réf : {{ $m->reference ?? '-' }}</div>
                                    </td>

                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-surface text-content-secondary">
                                            {{ $m->categorie->nom ?? 'Non classé' }}
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full font-bold text-xs {{ $enStock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-surface text-content-secondary' }}">
                                            {{ $enStock }}
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full font-bold text-xs {{ $enUtil > 0 ? 'bg-blue-50 text-primary' : 'bg-surface text-content-secondary' }}">
                                            {{ $enUtil }}
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full font-bold text-xs {{ $enPanne > 0 ? 'bg-rose-50 text-rose-700' : 'bg-surface text-content-secondary' }}">
                                            {{ $enPanne }}
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-4 text-center font-extrabold text-content text-sm">
                                        {{ $total }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($materiels->hasPages())
                    <div class="p-4 border-t border-border/50">
                        {{ $materiels->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Modale d'envoi par Gmail -->
        <div
            x-show="showSendModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            x-transition
            style="display: none;"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="showSendModal = false"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="showSendModal = false"></div>

                <div class="relative bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl z-10">
                    <div class="flex items-start gap-3 pb-4 border-b border-border/60 mb-5">
                        <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                            {!! $svg('mail', 'w-5 h-5') !!}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-base font-bold text-content">Transmission du rapport de stock</h4>
                            <p class="text-xs text-content-secondary mt-0.5 break-all">Destinataire : {{ $destinataireConfig['email'] ?? 'Non configuré' }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6 text-xs text-content">
                        <div class="p-4 bg-amber-50 rounded-2xl text-amber-900 space-y-2">
                            <p class="font-bold flex items-center gap-2">
                                {!! $svg('info', 'w-4 h-4 text-amber-600') !!}
                                Procédure d'envoi par Gmail
                            </p>
                            <ol class="list-decimal list-inside space-y-1 text-[11px] text-amber-800">
                                <li>Le <strong>rapport PDF filtré</strong> va s'ouvrir pour être téléchargé / enregistré sur votre poste.</li>
                                <li><strong>Gmail</strong> s'ouvrira automatiquement avec un message prérempli destiné à la Direction (<span class="font-mono break-all">{{ $destinataireConfig['email'] ?? '' }}</span>).</li>
                                <li><strong>Joignez manuellement le PDF</strong> téléchargé dans Gmail avant de cliquer sur Envoyer.</li>
                            </ol>
                        </div>
                        <p class="text-content-secondary text-[11px] italic">
                            L'application ne prétend pas que le message ou le fichier a été envoyé automatiquement.
                        </p>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-border/60">
                        <button
                            type="button"
                            @click="showSendModal = false"
                            class="px-5 py-2.5 bg-surface rounded-full text-xs font-semibold text-content hover:bg-slate-100 transition-colors cursor-pointer"
                        >
                            Annuler
                        </button>
                        <button
                            type="button"
                            @click="triggerSend()"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-bold transition-colors cursor-pointer"
                        >
                            {!! $svg('external-link', 'w-4 h-4') !!}
                            Générer le PDF & Ouvrir Gmail
                        </button>
                    </div>
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

                    const pdfUrl = '{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}';
                    window.open(pdfUrl, '_blank');

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