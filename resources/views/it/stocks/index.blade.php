@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'plus'               => 'M5 12h14|M12 5v14',
        'x'                  => 'M18 6 6 18|m6 6 12 12',
        'package'            => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'boxes'              => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'laptop'             => 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16',
        'alert-octagon'      => 'M12 16h.01|M12 8v4|M15.312 2a2 2 0 0 1 1.414.586l4.688 4.688A2 2 0 0 1 22 8.688v6.624a2 2 0 0 1-.586 1.414l-4.688 4.688a2 2 0 0 1-1.414.586H8.688a2 2 0 0 1-1.414-.586l-4.688-4.688A2 2 0 0 1 2 15.312V8.688a2 2 0 0 1 .586-1.414l4.688-4.688A2 2 0 0 1 8.688 2z',
        'alert-triangle'     => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'check-circle'       => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'arrow-up-right'     => 'M7 7h10v10|M7 17 17 7',
        'arrow-down-to-line' => 'M12 17V3|m6 11 6 6 6-6|M19 21H5',
        'arrow-up-from-line' => 'm18 9-6-6-6 6|M12 3v14|M5 21h14',
        'arrow-left-right'   => 'M8 3 4 7l4 4|M4 7h16|m16 21 4-4-4-4|M20 17H4',
        'refresh-cw'         => 'M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8|M21 3v5h-5|M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16|M8 16H3v5',
        'wrench'             => 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z',
        'pie-chart'          => 'M21.21 15.89A10 10 0 1 1 8 2.83|M22 12A10 10 0 0 0 12 2v10z',
        'mail'               => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'search'             => 'm21 21-4.34-4.34|M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16z',
        // Icônes par type de matériel
        'monitor'            => 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M8 21h8|M12 17v4',
        'cpu'                => 'M12 20v2|M12 2v2|M17 20v2|M17 2v2|M2 12h2|M2 17h2|M2 7h2|M20 12h2|M20 17h2|M20 7h2|M7 20v2|M7 2v2|M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z|M9 9h6v6H9z',
        'mouse'              => 'M12 2a7 7 0 0 0-7 7v6a7 7 0 0 0 14 0V9a7 7 0 0 0-7-7z|M12 6v4',
        'keyboard'           => 'M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z|M6 8h.01|M10 8h.01|M14 8h.01|M18 8h.01|M8 12h.01|M12 12h.01|M16 12h.01|M7 16h10',
        'printer'            => 'M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2|M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6|M8 14h8v7a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1z',
        'headphones'         => 'M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3',
        'smartphone'         => 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M12 18h.01',
        'tablet'             => 'M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M12 18h.01',
        'hard-drive'         => 'M22 12H2|M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z|M6 16h.01|M10 16h.01',
        'server'             => 'M4 2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M4 14h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2z|M6 6h.01|M6 18h.01',
        'wifi'               => 'M12 20h.01|M2 8.82a15 15 0 0 1 20 0|M5 12.859a10 10 0 0 1 14 0|M8.5 16.429a5 5 0 0 1 7 0',
        'plug'               => 'M12 22v-5|M9 8V2|M15 8V2|M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z',
        'video'              => 'm16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5|M4 6h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? $icons['package']) as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };

    // Icône par type de matériel : le nom du produit décide, sinon la catégorie, sinon colis.
    $iconRules = [
        'mouse'      => ['souris', 'mouse'],
        'keyboard'   => ['clavier', 'keyboard'],
        'printer'    => ['imprimante', 'printer', 'scanner', 'toner', 'cartouche'],
        'headphones' => ['casque', 'ecouteur', 'headset', 'audio', 'micro'],
        'video'      => ['webcam', 'camera'],
        'smartphone' => ['telephone', 'smartphone', 'mobile', 'iphone'],
        'tablet'     => ['tablette', 'ipad'],
        'server'     => ['serveur', 'server', 'rack'],
        'wifi'       => ['routeur', 'switch', 'reseau', 'wifi', 'modem', 'borne', 'access point'],
        'hard-drive' => ['disque', 'ssd', 'hdd', 'usb', 'stockage', 'cle ', 'memoire', 'ram'],
        'plug'       => ['cable', 'chargeur', 'adaptateur', 'alimentation', 'prise', 'multiprise', 'onduleur'],
        'cpu'        => ['unite centrale', 'tour', 'desktop', 'fixe', 'mini pc', 'processeur'],
        'laptop'     => ['portable', 'laptop', 'notebook', 'macbook'],
        'monitor'    => ['ecran', 'moniteur', 'display', 'ordinateur', 'pc'],
    ];
    $matchIcon = function (string $raw) use ($iconRules) {
        $text = ' ' . \Illuminate\Support\Str::ascii(mb_strtolower($raw)) . ' ';
        foreach ($iconRules as $icon => $keywords) {
            foreach ($keywords as $kw) {
                if ($kw === 'pc') {
                    if (preg_match('/\bpc\b/', $text)) { return $icon; }
                } elseif (str_contains($text, $kw)) {
                    return $icon;
                }
            }
        }
        return null;
    };
    $iconFor = function ($p) use ($matchIcon) {
        return $matchIcon($p->nom ?? ($p->designation ?? ''))
            ?? $matchIcon($p->categorie->nom ?? '')
            ?? 'package';
    };
@endphp

<x-layouts.dashboard title="Gestion du stock" :breadcrumbs="['Gestion du stock' => null]">

    <div
        x-data="{
            // Modale 1 : Entrée de stock (+)
            entreeModalOpen: false,
            entreeProduct: { id: null, nom: '', ref: '', en_stock: 0, total: 0 },
            entreeQuantite: 1,
            get entreeStockActuel() {
                return this.entreeProduct?.en_stock || 0;
            },
            get entreeNouveauStock() {
                const q = parseInt(this.entreeQuantite) || 0;
                return this.entreeStockActuel + Math.max(0, q);
            },
            openEntreeModal(prod) {
                this.entreeProduct = prod;
                this.entreeQuantite = 1;
                this.entreeModalOpen = true;
            },

            // Modale 2 : Sortie définitive (−)
            sortieModalOpen: false,
            sortieProduct: { id: null, nom: '', ref: '', en_stock: 0 },
            sortieSituation: 'en_stock',
            sortieQuantite: 1,
            get sortieStockActuel() {
                if (!this.sortieProduct) return 0;
                return this.sortieProduct.en_stock || 0;
            },
            openSortieModal(prod) {
                this.sortieProduct = prod;
                this.sortieQuantite = 1;
                this.sortieSituation = 'en_stock';
                this.sortieModalOpen = true;
            },

            // Modale 3 : Changer la situation (En stock ↔ En utilisation ↔ En panne)
            situationModalOpen: false,
            situationProduct: { id: null, nom: '', ref: '', en_stock: 0, en_utilisation: 0, en_panne: 0, total: 0 },
            situationDepart: 'en_stock',
            situationArrivee: 'en_utilisation',
            situationQuantite: 1,
            situationConfirmeFonctionnel: false,
            get maxDisponible() {
                if (!this.situationProduct) return 0;
                if (this.situationDepart === 'en_stock') return parseInt(this.situationProduct.en_stock) || 0;
                if (this.situationDepart === 'en_utilisation') return parseInt(this.situationProduct.en_utilisation) || 0;
                if (this.situationDepart === 'en_panne') return parseInt(this.situationProduct.en_panne) || 0;
                return 0;
            },
            get situationValide() {
                const q = parseInt(this.situationQuantite) || 0;
                if (q <= 0 || q > this.maxDisponible) return false;
                if (this.situationDepart === this.situationArrivee) return false;
                if (this.situationDepart === 'en_panne' && !this.situationConfirmeFonctionnel) return false;
                return true;
            },
            openSituationModal(prod, depart = 'en_stock', arrivee = 'en_utilisation') {
                this.situationProduct = prod;
                this.situationDepart = depart;
                this.situationArrivee = arrivee;
                this.situationQuantite = 1;
                this.situationConfirmeFonctionnel = false;
                this.situationModalOpen = true;
            },

            // Alertes repliables
            showAllAlerts: false
        }"
        @keydown.escape.window="entreeModalOpen = false; sortieModalOpen = false; situationModalOpen = false"
    >
        <!-- Messages flash -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                {!! $svg('check-circle', 'w-5 h-5 text-emerald-600 flex-shrink-0') !!}
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 bg-rose-50 rounded-2xl flex items-center gap-3 text-rose-800 text-sm">
                {!! $svg('alert-triangle', 'w-5 h-5 text-rose-600 flex-shrink-0') !!}
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

    

        <!-- Alertes de stock -->
        @if (!empty($alertesStock) && count($alertesStock) > 0)
            <div class="mb-6 bg-white rounded-3xl p-4 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            {!! $svg('alert-triangle', 'w-4 h-4') !!}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-content">Alertes de stock</h3>
                            <p class="text-xs text-content-secondary">Produits dont le stock disponible en réserve est insuffisant.</p>
                        </div>
                    </div>
                    <span class="self-start sm:self-auto inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">
                        {{ count($alertesStock) }} produit{{ count($alertesStock) > 1 ? 's' : '' }} en alerte
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-3">
                    @foreach ($alertesStock as $index => $alerte)
                        @php $isRupture = ($alerte['type'] === 'rupture'); @endphp
                        <div class="p-4 rounded-2xl bg-surface/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $isRupture ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                                    {!! $svg($iconFor($alerte['produit'])) !!}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-content break-words" title="{{ $alerte['nom'] }}">{{ $alerte['nom'] }}</h4>
                                        @if ($isRupture)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Épuisé (0 en réserve)</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Stock faible (≤ {{ $alerte['seuil'] }})</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-content-secondary mt-0.5">
                                        <span class="font-mono">Réf : {{ $alerte['produit']->reference }}</span>
                                        <span>&bull;</span>
                                        <span class="font-semibold {{ $isRupture ? 'text-red-700' : 'text-amber-700' }}">{{ $alerte['quantite'] }} unité(s) en réserve</span>
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ $alerte['gmail_url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold text-white transition-colors flex-shrink-0 {{ $isRupture ? 'bg-red-500 hover:bg-red-600' : 'bg-[#FE7A04] hover:bg-[#e66d00]' }}"
                                title="Ouvrir Gmail avec le message prérempli"
                            >
                                {!! $svg('mail', 'w-4 h-4') !!}
                                <span>Demander</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 4 cartes statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            {!! $svg('boxes') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">En stock</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($kpis['total_en_stock']) }}</p>
                <p class="text-xs text-content-secondary mt-1">Disponible en réserve</p>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                            {!! $svg('laptop') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">En utilisation</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($kpis['total_en_utilisation']) }}</p>
                <p class="text-xs text-content-secondary mt-1">Postes et production</p>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full {{ $kpis['total_en_panne'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-surface text-content-secondary' }} flex items-center justify-center flex-shrink-0">
                            {!! $svg('alert-octagon') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">En panne</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold {{ $kpis['total_en_panne'] > 0 ? 'text-rose-600' : 'text-content' }} tracking-tight">{{ number_format($kpis['total_en_panne']) }}</p>
                <p class="text-xs text-content-secondary mt-1">Hors service / à réparer</p>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                            {!! $svg('pie-chart') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">Total global</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($kpis['total_global']) }}</p>
                <p class="text-xs text-content-secondary mt-1">Toutes situations réunies</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 mb-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <form method="GET" action="{{ route('it.stocks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Recherche -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-secondary">
                        {!! $svg('search', 'w-4 h-4') !!}
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Rechercher nom, référence..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                </div>

                <!-- Catégorie -->
                <div>
                    <select
                        name="category_id"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Situation -->
                <div>
                    <select
                        name="situation"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Toutes les situations</option>
                        <option value="en_stock" {{ $situation === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ $situation === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ $situation === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- Niveau du stock & réinitialiser -->
                <div class="flex items-center gap-2">
                    <select
                        name="stock_alert"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Tous les états</option>
                        <option value="disponible" {{ $stockAlert === 'disponible' || $stockAlert === 'normal' ? 'selected' : '' }}>Disponible</option>
                        <option value="faible" {{ $stockAlert === 'faible' ? 'selected' : '' }}>Stock faible</option>
                        <option value="rupture" {{ $stockAlert === 'rupture' ? 'selected' : '' }}>Épuisé</option>
                    </select>

                    @if ($search || $categoryId || $situation || $stockAlert)
                        <a
                            href="{{ route('it.stocks.index') }}"
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
        <div class="mb-10 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] overflow-hidden">
            @if ($produits->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        {!! $svg('boxes', 'w-8 h-8') !!}
                    </div>
                    <h3 class="text-base font-bold text-content">Aucun matériel trouvé</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">
                        @if ($search || $categoryId || $emplacementId || $situation || $stockAlert)
                            Aucun produit ne correspond à vos filtres actuels.
                        @else
                            Aucun produit n'est actuellement consigné en stock.
                        @endif
                    </p>
                    <div class="mt-5">
                        @if ($search || $categoryId || $emplacementId || $situation || $stockAlert)
                            <a href="{{ route('it.stocks.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-surface text-content text-xs font-semibold rounded-full hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Mobile / tablette : cartes -->
                <div class="lg:hidden p-4 space-y-3">
                    @foreach ($produits as $produit)
                        @php
                            $nomProd = $produit->nom ?? $produit->designation;
                            $enStock = $produit->quantite_en_stock;
                            $enUtil = $produit->quantite_en_utilisation;
                            $enPanne = $produit->quantite_en_panne;
                            $total = $produit->total_stock;
                            $ico = $iconFor($produit);
                            $dEntree = ['id' => $produit->id, 'nom' => $nomProd, 'ref' => $produit->reference, 'en_stock' => $enStock, 'total' => $total];
                            $dSortie = ['id' => $produit->id, 'nom' => $nomProd, 'ref' => $produit->reference, 'en_stock' => $enStock];
                            $dSit = ['id' => $produit->id, 'nom' => $nomProd, 'ref' => $produit->reference, 'en_stock' => $enStock, 'en_utilisation' => $enUtil, 'en_panne' => $enPanne, 'total' => $total];
                        @endphp
                        <div class="p-4 rounded-2xl bg-surface/60">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if ($produit->photo_url)
                                        <img src="{{ $produit->photo_url }}" alt="{{ $nomProd }}" class="w-full h-full object-cover" />
                                    @else
                                        {!! $svg($ico) !!}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('it.products.show', $produit) }}" class="text-sm font-bold text-content hover:text-[#0092FD] transition-colors break-words block">
                                        {{ $nomProd }}
                                    </a>
                                    <div class="flex items-center gap-2 flex-wrap text-[11px] text-content-secondary mt-0.5">
                                        <span class="font-mono">Réf : {{ $produit->reference }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $produit->categorie->nom ?? 'Non classé' }}</span>
                                    </div>
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

                            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button type="button" @click="openEntreeModal({{ \Illuminate\Support\Js::from($dEntree) }})"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors cursor-pointer">
                                    {!! $svg('arrow-down-to-line', 'w-4 h-4') !!} Entrée
                                </button>
                                <button type="button" @click="openSortieModal({{ \Illuminate\Support\Js::from($dSortie) }})"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-full bg-[#FE7A04] hover:bg-[#e66d00] text-white text-xs font-semibold transition-colors disabled:opacity-35 disabled:cursor-not-allowed cursor-pointer"
                                    {{ $enStock <= 0 ? 'disabled' : '' }}>
                                    {!! $svg('arrow-up-from-line', 'w-4 h-4') !!} Sortie
                                </button>
                                <button type="button" @click="openSituationModal({{ \Illuminate\Support\Js::from($dSit) }})"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold transition-colors cursor-pointer">
                                    {!! $svg('arrow-left-right', 'w-4 h-4') !!} Situation
                                </button>
                                <a href="{{ route('it.demandes.create', ['produit_id' => $produit->id]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-full bg-white text-content text-xs font-semibold hover:text-[#FE7A04] transition-colors">
                                    {!! $svg('mail', 'w-4 h-4') !!} Demander
                                </a>
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
                                <th class="py-3 px-4 text-center">En stock</th>
                                <th class="py-3 px-4 text-center">En utilisation</th>
                                <th class="py-3 px-4 text-center">En panne</th>
                                <th class="py-3 px-4 text-center">Total</th>
                                <th class="py-3 px-5 text-right rounded-r-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produits as $produit)
                                @php
                                    $nomProd = $produit->nom ?? $produit->designation;
                                    $enStock = $produit->quantite_en_stock;
                                    $enUtil = $produit->quantite_en_utilisation;
                                    $enPanne = $produit->quantite_en_panne;
                                    $total = $produit->total_stock;
                                    $ico = $iconFor($produit);
                                    $dEntree = ['id' => $produit->id, 'nom' => $nomProd, 'ref' => $produit->reference, 'en_stock' => $enStock, 'total' => $total];
                                    $dSortie = ['id' => $produit->id, 'nom' => $nomProd, 'ref' => $produit->reference, 'en_stock' => $enStock];
                                    $dSit = ['id' => $produit->id, 'nom' => $nomProd, 'ref' => $produit->reference, 'en_stock' => $enStock, 'en_utilisation' => $enUtil, 'en_panne' => $enPanne, 'total' => $total];
                                @endphp
                                <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                    <!-- Matériel -->
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if ($produit->photo_url)
                                                    <img src="{{ $produit->photo_url }}" alt="{{ $nomProd }}" class="w-full h-full object-cover" />
                                                @else
                                                    {!! $svg($ico) !!}
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('it.products.show', $produit) }}" class="font-bold text-content hover:text-[#0092FD] transition-colors truncate block">
                                                    {{ $nomProd }}
                                                </a>
                                                <div class="flex items-center gap-2 text-[11px] text-content-secondary mt-0.5">
                                                    <span class="font-mono text-content-secondary/80">Réf : {{ $produit->reference }}</span>
                                                    <span>&bull;</span>
                                                    <span>{{ $produit->categorie->nom ?? 'Non classé' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- En stock -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full font-bold text-xs {{ $enStock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-surface text-content-secondary' }}">
                                            {{ $enStock }}
                                        </span>
                                    </td>

                                    <!-- En utilisation -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full font-bold text-xs {{ $enUtil > 0 ? 'bg-blue-50 text-primary' : 'bg-surface text-content-secondary' }}">
                                            {{ $enUtil }}
                                        </span>
                                    </td>

                                    <!-- En panne -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full font-bold text-xs {{ $enPanne > 0 ? 'bg-rose-50 text-rose-700' : 'bg-surface text-content-secondary' }}">
                                            {{ $enPanne }}
                                        </span>
                                    </td>

                                    <!-- Total -->
                                    <td class="py-3.5 px-4 text-center font-extrabold text-content text-sm">
                                        {{ $total }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Entrée en réserve -->
                                            <button
                                                type="button"
                                                @click="openEntreeModal({{ \Illuminate\Support\Js::from($dEntree) }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors active:scale-95 cursor-pointer"
                                                title="Entrée en réserve"
                                            >
                                                {!! $svg('arrow-down-to-line', 'w-4 h-4') !!}
                                            </button>

                                            <!-- Sortie définitive -->
                                            <button
                                                type="button"
                                                @click="openSortieModal({{ \Illuminate\Support\Js::from($dSortie) }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] hover:bg-[#FE7A04] hover:text-white transition-colors active:scale-95 disabled:opacity-35 disabled:cursor-not-allowed cursor-pointer"
                                                {{ $enStock <= 0 ? 'disabled' : '' }}
                                                title="Sortie définitive"
                                            >
                                                {!! $svg('arrow-up-from-line', 'w-4 h-4') !!}
                                            </button>

                                            <!-- Changer la situation -->
                                            <button
                                                type="button"
                                                @click="openSituationModal({{ \Illuminate\Support\Js::from($dSit) }})"
                                                class="inline-flex items-center justify-center px-3 h-8 rounded-full bg-[#0092FD]/10 text-[#0092FD] hover:bg-[#0092FD] hover:text-white font-semibold text-xs transition-colors active:scale-95 cursor-pointer gap-1.5"
                                                title="Changer la situation (En stock ↔ En utilisation ↔ En panne)"
                                            >
                                                {!! $svg('arrow-left-right', 'w-3.5 h-3.5') !!}
                                                <span>Situation</span>
                                            </button>

                                            <!-- Demande par Gmail -->
                                            <a
                                                href="{{ route('it.demandes.create', ['produit_id' => $produit->id]) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-content-secondary hover:text-[#FE7A04] hover:bg-[#FE7A04]/10 rounded-full transition-colors"
                                                title="Demander du matériel"
                                            >
                                                {!! $svg('mail', 'w-4 h-4') !!}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($produits->hasPages())
                    <div class="p-4 border-t border-border/50">
                        {{ $produits->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- ========================================================= -->
        <!-- MODALE 1 : ENTRÉE EN STOCK                                -->
        <!-- ========================================================= -->
        <div x-show="entreeModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="entreeModalOpen" class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="entreeModalOpen = false"></div>

                <div x-show="entreeModalOpen" class="relative bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl z-10">
                    <div class="flex items-center justify-between pb-4 border-b border-border/60 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                {!! $svg('arrow-down-to-line') !!}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content">Entrée de stock</h3>
                                <p class="text-xs text-content-secondary">Réception de matériel en réserve</p>
                            </div>
                        </div>
                        <button type="button" @click="entreeModalOpen = false" class="w-9 h-9 flex items-center justify-center rounded-full text-content-secondary hover:text-content hover:bg-surface transition-colors flex-shrink-0">
                            {!! $svg('x', 'w-5 h-5') !!}
                        </button>
                    </div>

                    <form method="POST" action="{{ route('it.stocks.entree.store') }}">
                        @csrf
                        <input type="hidden" name="produit_id" :value="entreeProduct.id">

                        <div class="space-y-4 text-left">
                            <div class="bg-surface/60 p-4 rounded-2xl">
                                <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Matériel sélectionné</span>
                                <span class="text-sm font-bold text-content block mt-0.5 break-words" x-text="entreeProduct.nom"></span>
                                <span class="text-[11px] font-mono text-content-secondary" x-text="'Réf : ' + entreeProduct.ref"></span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Quantité à réceptionner</label>
                                <input
                                    type="number"
                                    name="quantite"
                                    x-model="entreeQuantite"
                                    min="1"
                                    required
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-2xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500"
                                >
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-border/60">
                            <button type="button" @click="entreeModalOpen = false" class="px-5 py-2.5 bg-surface rounded-full text-xs font-semibold text-content hover:bg-slate-100 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full text-xs font-bold transition-colors">
                                Confirmer l'entrée
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODALE 2 : SORTIE DÉFINITIVE                              -->
        <!-- ========================================================= -->
        <div x-show="sortieModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="sortieModalOpen" class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="sortieModalOpen = false"></div>

                <div x-show="sortieModalOpen" class="relative bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl z-10">
                    <div class="flex items-center justify-between pb-4 border-b border-border/60 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] flex items-center justify-center flex-shrink-0">
                                {!! $svg('arrow-up-from-line') !!}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content">Sortie définitive</h3>
                                <p class="text-xs text-content-secondary">Mise au rebut, perte ou sortie définitive du stock</p>
                            </div>
                        </div>
                        <button type="button" @click="sortieModalOpen = false" class="w-9 h-9 flex items-center justify-center rounded-full text-content-secondary hover:text-content hover:bg-surface transition-colors flex-shrink-0">
                            {!! $svg('x', 'w-5 h-5') !!}
                        </button>
                    </div>

                    <form method="POST" action="{{ route('it.stocks.sortie.store') }}">
                        @csrf
                        <input type="hidden" name="produit_id" :value="sortieProduct.id">

                        <div class="space-y-4 text-left">
                            <div class="bg-surface/60 p-4 rounded-2xl">
                                <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Matériel</span>
                                <span class="text-sm font-bold text-content block mt-0.5 break-words" x-text="sortieProduct.nom"></span>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mt-1 text-xs">
                                    <span class="text-content-secondary font-mono" x-text="'Réf : ' + sortieProduct.ref"></span>
                                    <span class="font-bold text-emerald-700" x-text="sortieStockActuel + ' unité(s) en réserve'"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Quantité à sortir définitivement</label>
                                <input
                                    type="number"
                                    name="quantite"
                                    x-model="sortieQuantite"
                                    min="1"
                                    :max="sortieStockActuel"
                                    required
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-2xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#FE7A04]/25 focus:border-[#FE7A04]"
                                >
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-border/60">
                            <button type="button" @click="sortieModalOpen = false" class="px-5 py-2.5 bg-surface rounded-full text-xs font-semibold text-content hover:bg-slate-100 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-[#FE7A04] hover:bg-[#e66d00] text-white rounded-full text-xs font-bold transition-colors">
                                Confirmer la sortie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODALE 3 : CHANGER LA SITUATION DU MATÉRIEL               -->
        <!-- ========================================================= -->
        <div x-show="situationModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="situationModalOpen" class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="situationModalOpen = false"></div>

                <div x-show="situationModalOpen" class="relative bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl z-10">
                    <div class="flex items-center justify-between pb-4 border-b border-border/60 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                {!! $svg('arrow-left-right') !!}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content">Changer la situation</h3>
                                <p class="text-xs text-content-secondary">Répartition interne : Réserve, Utilisation, Panne</p>
                            </div>
                        </div>
                        <button type="button" @click="situationModalOpen = false" class="w-9 h-9 flex items-center justify-center rounded-full text-content-secondary hover:text-content hover:bg-surface transition-colors flex-shrink-0">
                            {!! $svg('x', 'w-5 h-5') !!}
                        </button>
                    </div>

                    <form method="POST" action="{{ route('it.stocks.situation.store') }}">
                        @csrf
                        <input type="hidden" name="produit_id" :value="situationProduct.id">

                        <div class="space-y-4 text-left">
                            <!-- Matériel -->
                            <div class="bg-surface/60 p-4 rounded-2xl">
                                <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Matériel</span>
                                <span class="text-sm font-bold text-content block mt-0.5 break-words" x-text="situationProduct.nom"></span>
                                <div class="flex items-center gap-x-3 gap-y-1 mt-1.5 text-xs text-content-secondary flex-wrap">
                                    <span>Stock réserve : <strong class="text-emerald-700" x-text="situationProduct.en_stock || 0"></strong></span>
                                    <span>&bull;</span>
                                    <span>En utilisation : <strong class="text-primary" x-text="situationProduct.en_utilisation || 0"></strong></span>
                                    <span>&bull;</span>
                                    <span>En panne : <strong class="text-rose-600" x-text="situationProduct.en_panne || 0"></strong></span>
                                </div>
                            </div>

                            <!-- Situations départ & arrivée -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Situation actuelle</label>
                                    <select
                                        name="situation_depart"
                                        x-model="situationDepart"
                                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs font-semibold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD]"
                                    >
                                        <option value="en_stock">En stock (réserve)</option>
                                        <option value="en_utilisation">En utilisation</option>
                                        <option value="en_panne">En panne</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Nouvelle situation</label>
                                    <select
                                        name="situation_arrivee"
                                        x-model="situationArrivee"
                                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs font-semibold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD]"
                                    >
                                        <option value="en_utilisation">En utilisation</option>
                                        <option value="en_stock">En stock (réserve)</option>
                                        <option value="en_panne">En panne</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Quantité -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5 gap-2">
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider">Quantité</label>
                                    <span class="text-[11px] font-semibold text-content-secondary">
                                        Disponible : <span class="font-bold text-[#0092FD]" x-text="maxDisponible + ' unité(s)'"></span>
                                    </span>
                                </div>
                                <input
                                    type="number"
                                    name="quantite"
                                    x-model="situationQuantite"
                                    min="1"
                                    :max="maxDisponible"
                                    required
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-2xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD]"
                                >
                            </div>

                            <!-- Confirmation pour remise en service d'un matériel en panne -->
                            <div x-show="situationDepart === 'en_panne'" class="p-4 bg-amber-50 rounded-2xl">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="confirme_fonctionnel"
                                        value="1"
                                        x-model="situationConfirmeFonctionnel"
                                        class="mt-0.5 rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                                    >
                                    <span class="text-xs font-semibold text-amber-900 leading-snug">
                                        Je confirme que ce matériel a été contrôlé/réparé et est de nouveau fonctionnel avant de le remettre en service.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-border/60">
                            <button
                                type="button"
                                @click="situationModalOpen = false"
                                class="px-5 py-2.5 bg-surface rounded-full text-xs font-semibold text-content hover:bg-slate-100 transition-colors"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                :disabled="!situationValide"
                                class="px-6 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white rounded-full text-xs font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                Confirmer le changement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>