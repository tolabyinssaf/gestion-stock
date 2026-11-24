@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'bar-chart-3'      => 'M3 3v16a2 2 0 0 0 2 2h16|M18 17V9|M13 17V5|M8 17v-3',
        'mail'             => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'boxes'            => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'history'          => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8|M3 3v5h5|M12 7v5l4 2',
        'check'            => 'M20 6 9 17l-5-5',
        'eye'              => 'M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0|M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
        'download'         => 'M12 15V3|M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4|m7 10 5 5 5-5',
        'chevron-down'     => 'm6 9 6 6 6-6',
        'file-text'        => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M10 9H8|M16 13H8|M16 17H8',
        'file-spreadsheet' => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M8 13h2|M14 13h2|M8 17h2|M14 17h2',
        'send'             => 'M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z|m21.854 2.147-10.94 10.939',
        'info'             => 'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0z|M12 16v-4|M12 8h.01',
        'external-link'    => 'M15 3h6v6|M10 14 21 3|M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Rapports & Exports" :breadcrumbs="['Rapports & Exports' => null]">

    <div x-data="reportsHub()" @keydown.escape.window="showSendModal = false">

        <!-- En-tête -->
        <div class="mb-6 p-5 sm:p-6 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-6">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('bar-chart-3', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-extrabold text-content tracking-tight">Rapports & Exports</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-0.5 max-w-2xl">
                        Consultez, exportez et partagez vos rapports.
                    </p>
                </div>
            </div>

            <div class="flex items-start sm:items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-surface/70 text-xs text-content-secondary self-stretch lg:self-center min-w-0">
                <span class="mt-0.5 sm:mt-0 flex-shrink-0 text-[#0092FD]">{!! $svg('mail', 'w-4 h-4') !!}</span>
                <span class="min-w-0 break-words">Destinataire des rapports : <strong class="text-content">{{ $destinataireEmail }}</strong> ({{ $destinataireNom }})</span>
            </div>
        </div>

        <!-- Cartes de rapports -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8">

            <!-- Rapport de stock -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] flex flex-col justify-between transition-all">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                            {!! $svg('boxes', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#0092FD]/10 text-[#0092FD]">
                            Stock & Inventaire
                        </span>
                    </div>

                    <h3 class="text-base sm:text-lg font-extrabold text-content mb-1.5">1. Rapport de stock</h3>
                    <p class="text-xs text-content-secondary leading-relaxed mb-5">
                        Inventaire complet des produits, catégories, quantités disponibles, emplacements de stockage et indicateurs d'état (disponible, stock faible, rupture).
                    </p>

                    <!-- Liste des fonctionnalités -->
                    <div class="space-y-2.5 mb-6 text-xs text-content-secondary bg-surface/60 p-4 rounded-2xl">
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">{!! $svg('check', 'w-3 h-3') !!}</span>
                            <span>Suivi en temps réel des unités en stock</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">{!! $svg('check', 'w-3 h-3') !!}</span>
                            <span>Filtres par catégorie, emplacement et niveau d'alerte</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">{!! $svg('check', 'w-3 h-3') !!}</span>
                            <span>Export certifié et transmission par email</span>
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <a
                        href="{{ route('it.reports.inventaire') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors"
                    >
                        {!! $svg('eye', 'w-4 h-4') !!}
                        Consulter
                    </a>

                    <div class="flex items-center gap-2 sm:justify-end">
                        <div class="relative flex-1 sm:flex-none" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-full bg-surface hover:bg-slate-100 text-xs font-semibold text-content transition-colors cursor-pointer"
                            >
                                {!! $svg('download', 'w-4 h-4 text-content-secondary') !!}
                                <span>Exporter</span>
                                {!! $svg('chevron-down', 'w-3.5 h-3.5 text-content-secondary') !!}
                            </button>
                            <div
                                x-show="open"
                                x-transition
                                class="absolute left-0 sm:left-auto sm:right-0 bottom-full mb-2 w-56 bg-white rounded-2xl shadow-[0_8px_30px_rgba(2,45,119,0.18)] p-1.5 z-20 space-y-1"
                                style="display: none;"
                            >
                                <a
                                    href="{{ route('it.reports.inventaire', ['export' => 'pdf']) }}"
                                    target="_blank"
                                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    {!! $svg('file-text', 'w-4 h-4 text-rose-600') !!}
                                    <span>Télécharger PDF</span>
                                </a>
                                <a
                                    href="{{ route('it.reports.inventaire', ['export' => 'excel']) }}"
                                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    {!! $svg('file-spreadsheet', 'w-4 h-4 text-emerald-600') !!}
                                    <span>Exporter Excel (.csv)</span>
                                </a>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="handleSend('{{ route('it.reports.inventaire', ['action' => 'get_gmail_url']) }}', '{{ route('it.reports.inventaire', ['export' => 'pdf']) }}', 'Rapport de stock')"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-full bg-[#0092FD]/10 hover:bg-[#0092FD] text-[#0092FD] hover:text-white text-xs font-bold transition-colors cursor-pointer"
                            title="Télécharger le PDF et préparer le courriel Gmail"
                        >
                            {!! $svg('send', 'w-4 h-4') !!}
                            <span>Envoyer</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rapport des mouvements -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] flex flex-col justify-between transition-all">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] flex items-center justify-center flex-shrink-0">
                            {!! $svg('history', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#FE7A04]/10 text-[#FE7A04]">
                            Traçabilité & Flux
                        </span>
                    </div>

                    <h3 class="text-base sm:text-lg font-extrabold text-content mb-1.5">2. Rapport des mouvements</h3>
                    <p class="text-xs text-content-secondary leading-relaxed mb-5">
                        Journal complet des entrées, sorties, transferts inter-magasins et corrections de stock, avec dates précises, quantités et utilisateurs responsables.
                    </p>

                    <div class="space-y-2.5 mb-6 text-xs text-content-secondary bg-surface/60 p-4 rounded-2xl">
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">{!! $svg('check', 'w-3 h-3') !!}</span>
                            <span>Traçabilité rigoureuse de chaque unité déplacée</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">{!! $svg('check', 'w-3 h-3') !!}</span>
                            <span>Filtres par date (période), type d'opération et motif</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">{!! $svg('check', 'w-3 h-3') !!}</span>
                            <span>Audit des utilisateurs à l'origine de l'opération</span>
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <a
                        href="{{ route('it.reports.mouvements') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#FE7A04] hover:bg-[#e66d00] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(254,122,4,0.35)] transition-colors"
                    >
                        {!! $svg('eye', 'w-4 h-4') !!}
                        Consulter
                    </a>

                    <div class="flex items-center gap-2 sm:justify-end">
                        <div class="relative flex-1 sm:flex-none" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-full bg-surface hover:bg-slate-100 text-xs font-semibold text-content transition-colors cursor-pointer"
                            >
                                {!! $svg('download', 'w-4 h-4 text-content-secondary') !!}
                                <span>Exporter</span>
                                {!! $svg('chevron-down', 'w-3.5 h-3.5 text-content-secondary') !!}
                            </button>
                            <div
                                x-show="open"
                                x-transition
                                class="absolute left-0 sm:left-auto sm:right-0 bottom-full mb-2 w-56 bg-white rounded-2xl shadow-[0_8px_30px_rgba(2,45,119,0.18)] p-1.5 z-20 space-y-1"
                                style="display: none;"
                            >
                                <a
                                    href="{{ route('it.reports.mouvements', ['export' => 'pdf']) }}"
                                    target="_blank"
                                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    {!! $svg('file-text', 'w-4 h-4 text-rose-600') !!}
                                    <span>Télécharger PDF</span>
                                </a>
                                <a
                                    href="{{ route('it.reports.mouvements', ['export' => 'excel']) }}"
                                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    {!! $svg('file-spreadsheet', 'w-4 h-4 text-emerald-600') !!}
                                    <span>Exporter Excel (.csv)</span>
                                </a>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="handleSend('{{ route('it.reports.mouvements', ['action' => 'get_gmail_url']) }}', '{{ route('it.reports.mouvements', ['export' => 'pdf']) }}', 'Rapport des mouvements')"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-full bg-[#FE7A04]/10 hover:bg-[#FE7A04] text-[#FE7A04] hover:text-white text-xs font-bold transition-colors cursor-pointer"
                            title="Télécharger le PDF et préparer le courriel Gmail"
                        >
                            {!! $svg('send', 'w-4 h-4') !!}
                            <span>Envoyer</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- MODALE : TRANSMISSION DU RAPPORT VIA GMAIL                -->
        <!-- ========================================================= -->
        <div
            x-show="showSendModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            x-transition
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="showSendModal = false"></div>

                <div class="relative bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl z-10">
                    <div class="flex items-center justify-between pb-4 border-b border-border/60 mb-5 gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                {!! $svg('mail') !!}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-base font-bold text-content">Transmission du rapport via Gmail</h4>
                                <p class="text-xs text-content-secondary truncate" x-text="currentReportTitle"></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6 text-xs text-content">
                        <div class="p-4 bg-[#0092FD]/5 rounded-2xl text-content space-y-2">
                            <p class="font-bold flex items-center gap-1.5 text-[#022D77]">
                                {!! $svg('info', 'w-4 h-4 text-[#0092FD]') !!}
                                Comment fonctionne l'envoi ?
                            </p>
                            <ol class="list-decimal list-inside space-y-1.5 text-[11px] text-content-secondary leading-relaxed">
                                <li>Le <strong class="text-content">document PDF</strong> est ouvert dans un nouvel onglet pour impression / téléchargement.</li>
                                <li>Un <strong class="text-content">courriel prérempli</strong> s'ouvre dans Gmail avec le destinataire configuré (<span class="font-mono break-all" x-text="destinataireEmail"></span>).</li>
                                <li><strong class="text-content">Important :</strong> Vous devez <strong class="text-content">joindre manuellement le PDF</strong> téléchargé dans Gmail avant de cliquer sur Envoyer.</li>
                            </ol>
                        </div>
                        <p class="text-content-secondary text-[11px] italic">
                            L'application ne prétend pas que l'email est expédié automatiquement : vous gardez le contrôle final de l'envoi dans Gmail.
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
                            @click="executeSend()"
                            class="inline-flex items-center justify-center gap-1.5 px-6 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-bold transition-colors cursor-pointer"
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
        function reportsHub() {
            return {
                showSendModal: false,
                currentReportTitle: '',
                currentApiUrl: '',
                currentPdfUrl: '',
                destinataireEmail: '{{ $destinataireEmail }}',
                handleSend(apiUrl, pdfUrl, title) {
                    this.currentApiUrl = apiUrl;
                    this.currentPdfUrl = pdfUrl;
                    this.currentReportTitle = title;
                    this.showSendModal = true;
                },
                executeSend() {
                    const pdfUrl = this.currentPdfUrl;
                    const apiUrl = this.currentApiUrl;
                    this.showSendModal = false;

                    window.open(pdfUrl, '_blank');

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
                        console.error('Erreur lors de la préparation Gmail :', err);
                    });
                }
            };
        }
    </script>

</x-layouts.dashboard>