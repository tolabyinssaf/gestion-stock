@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'x'                  => 'M18 6 6 18|m6 6 12 12',
        'search'             => 'm21 21-4.34-4.34|M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16z',
        'filter'             => 'M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z',
        'history'            => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8|M3 3v5h5|M12 7v5l4 2',
        'check-circle'       => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'boxes'              => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'alert-octagon'      => 'M12 16h.01|M12 8v4|M15.312 2a2 2 0 0 1 1.414.586l4.688 4.688A2 2 0 0 1 22 8.688v6.624a2 2 0 0 1-.586 1.414l-4.688 4.688a2 2 0 0 1-1.414.586H8.688a2 2 0 0 1-1.414-.586l-4.688-4.688A2 2 0 0 1 2 15.312V8.688a2 2 0 0 1 .586-1.414l4.688-4.688A2 2 0 0 1 8.688 2z',
        'arrow-down-to-line' => 'M12 17V3|m6 11 6 6 6-6|M19 21H5',
        'arrow-up-from-line' => 'm18 9-6-6-6 6|M12 3v14|M5 21h14',
        'arrow-left-right'   => 'M8 3 4 7l4 4|M4 7h16|m16 21 4-4-4-4|M20 17H4',
        'corner-down-left'   => 'M20 4v7a4 4 0 0 1-4 4H4|m9 10-5 5 5 5',
        'wrench'             => 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z',
        'arrow-right'        => 'M5 12h14|m12 5 7 7-7 7',
        // Icônes par type de matériel
        'package'            => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'laptop'             => 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16',
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
        if (!$p) { return 'package'; }
        return $matchIcon($p->nom ?? ($p->designation ?? ''))
            ?? $matchIcon($p->categorie->nom ?? '')
            ?? 'package';
    };
@endphp

<x-layouts.dashboard title="Historique" :breadcrumbs="['Historique' => null]">

    <div>
        <!-- Messages flash -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                {!! $svg('check-circle', 'w-5 h-5 text-emerald-600 flex-shrink-0') !!}
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Barre d'actions -->
        <div class="mb-6 flex sm:justify-end">
            <a
                href="{{ route('it.stocks.index') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] transition-all"
            >
                {!! $svg('boxes', 'w-4 h-4 text-primary') !!}
                Gestion du stock
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 mb-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <form method="GET" action="{{ route('it.mouvements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Recherche -->
                <div class="sm:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-secondary">
                        {!! $svg('search', 'w-4 h-4') !!}
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Recherche réf, motif..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                </div>

                <!-- Type d'opération -->
                <div>
                    <select
                        name="type"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Tous les types</option>
                        <option value="entree" {{ request('type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie_definitive" {{ request('type') === 'sortie_definitive' || request('type') === 'sortie' ? 'selected' : '' }}>Sortie définitive</option>
                        <option value="mise_utilisation" {{ request('type') === 'mise_utilisation' ? 'selected' : '' }}>Mise en utilisation</option>
                        <option value="retour_stock" {{ request('type') === 'retour_stock' ? 'selected' : '' }}>Retour en stock</option>
                        <option value="declaration_panne" {{ request('type') === 'declaration_panne' ? 'selected' : '' }}>Déclaration de panne</option>
                        <option value="remise_service" {{ request('type') === 'remise_service' ? 'selected' : '' }}>Remise en service</option>
                        <option value="transfert" {{ request('type') === 'transfert' ? 'selected' : '' }}>Transfert</option>
                        <option value="correction" {{ request('type') === 'correction' ? 'selected' : '' }}>Correction</option>
                    </select>
                </div>

                <!-- Situation départ -->
                <div>
                    <select
                        name="situation_depart"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Situation départ (toutes)</option>
                        <option value="en_stock" {{ request('situation_depart') === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ request('situation_depart') === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ request('situation_depart') === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- Situation arrivée -->
                <div>
                    <select
                        name="situation_arrivee"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Situation arrivée (toutes)</option>
                        <option value="en_stock" {{ request('situation_arrivee') === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ request('situation_arrivee') === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ request('situation_arrivee') === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- Période + boutons -->
                <div class="sm:col-span-2 lg:col-span-3 flex flex-col sm:flex-row sm:items-center gap-2">
                    <div class="flex items-center gap-2 flex-1">
                        <input
                            type="date"
                            name="date_debut"
                            value="{{ request('date_debut') }}"
                            title="Date début"
                            class="w-1/2 min-w-0 px-3 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD]"
                        >
                        <span class="text-content-secondary flex-shrink-0">{!! $svg('arrow-right', 'w-4 h-4') !!}</span>
                        <input
                            type="date"
                            name="date_fin"
                            value="{{ request('date_fin') }}"
                            title="Date fin"
                            class="w-1/2 min-w-0 px-3 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD]"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white rounded-full text-xs font-semibold transition-colors cursor-pointer"
                        >
                            {!! $svg('filter', 'w-4 h-4') !!}
                            Filtrer
                        </button>
                        @if (request()->hasAny(['search', 'type', 'situation_depart', 'situation_arrivee', 'date_debut', 'date_fin']))
                            <a
                                href="{{ route('it.mouvements.index') }}"
                                class="p-2.5 bg-surface hover:bg-slate-100 text-content-secondary rounded-full transition-colors flex-shrink-0"
                                title="Réinitialiser"
                            >
                                {!! $svg('x', 'w-4 h-4') !!}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste des mouvements -->
        <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] overflow-hidden mb-8">
            @if ($mouvements->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        {!! $svg('history', 'w-8 h-8') !!}
                    </div>
                    <h3 class="text-base font-bold text-content">Aucun mouvement trouvé</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">Aucune opération ne correspond à vos filtres actuels.</p>
                </div>
            @else
                @php
                    // Prépare l'affichage de chaque mouvement (opération, icône, couleurs, quantité)
                    $rows = $mouvements->map(function ($mvt) {
                        $isEntree = $mvt->type === 'entree';
                        $isSortie = in_array($mvt->type, ['sortie', 'sortie_definitive']);
                        if ($isEntree) {
                            $op = ['Entrée', 'bg-emerald-50 text-emerald-700', 'arrow-down-to-line'];
                        } elseif ($isSortie) {
                            $op = ['Sortie définitive', 'bg-[#FE7A04]/10 text-[#FE7A04]', 'arrow-up-from-line'];
                        } elseif ($mvt->type === 'mise_utilisation') {
                            $op = ['Mise en utilisation', 'bg-blue-50 text-primary', 'laptop'];
                        } elseif ($mvt->type === 'retour_stock') {
                            $op = ['Retour en stock', 'bg-emerald-50 text-emerald-700', 'corner-down-left'];
                        } elseif ($mvt->type === 'declaration_panne') {
                            $op = ['Déclaration de panne', 'bg-rose-50 text-rose-700', 'alert-octagon'];
                        } elseif ($mvt->type === 'remise_service') {
                            $op = ['Remise en service', 'bg-emerald-50 text-emerald-700', 'check-circle'];
                        } elseif ($mvt->type === 'transfert') {
                            $op = [$mvt->type_label ?? ucfirst($mvt->type), 'bg-surface text-content', 'arrow-left-right'];
                        } elseif ($mvt->type === 'correction') {
                            $op = [$mvt->type_label ?? ucfirst($mvt->type), 'bg-surface text-content', 'wrench'];
                        } else {
                            $op = [$mvt->type_label ?? ucfirst($mvt->type), 'bg-surface text-content', 'history'];
                        }
                        return [
                            'mvt'      => $mvt,
                            'isEntree' => $isEntree,
                            'isSortie' => $isSortie,
                            'op'       => $op,
                        ];
                    });
                @endphp

                <!-- Mobile / tablette : cartes -->
                <div class="lg:hidden p-4 space-y-3">
                    @foreach ($rows as $row)
                        @php $mvt = $row['mvt']; $op = $row['op']; @endphp
                        <div class="p-4 rounded-2xl bg-surface/60">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                    {!! $svg($iconFor($mvt->produit)) !!}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-content break-words">{{ $mvt->produit->nom ?? $mvt->produit->designation ?? 'N/A' }}</p>
                                    <p class="text-[11px] text-content-secondary mt-0.5">Réf : {{ $mvt->produit->reference ?? '-' }}</p>
                                    <p class="font-mono text-[11px] font-bold text-content mt-0.5">{{ $mvt->reference }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-base font-extrabold">
                                        @if ($row['isEntree'])
                                            <span class="text-emerald-600">+{{ $mvt->quantite }}</span>
                                        @elseif ($row['isSortie'])
                                            <span class="text-rose-600">-{{ $mvt->quantite }}</span>
                                        @else
                                            <span class="text-content">{{ $mvt->quantite }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold {{ $op[1] }}">
                                    {!! $svg($op[2], 'w-3.5 h-3.5') !!}
                                    {{ $op[0] }}
                                </span>
                                @if ($mvt->situation_depart || $mvt->situation_arrivee)
                                    <span class="inline-flex items-center gap-1.5 text-[11px]">
                                        <span class="font-medium text-content">{{ $mvt->situation_depart_label ?? '—' }}</span>
                                        <span class="text-content-secondary">&rarr;</span>
                                        <span class="font-bold text-primary">{{ $mvt->situation_arrivee_label ?? '—' }}</span>
                                    </span>
                                @endif
                            </div>

                            @if ($mvt->motif)
                                <p class="mt-2 text-xs text-content break-words">{{ $mvt->motif }}</p>
                            @endif

                            <div class="mt-3 pt-3 border-t border-border/60 flex items-center justify-between gap-2 text-[11px] text-content-secondary">
                                <span class="text-content font-medium">{{ $mvt->user->name ?? 'Système' }}</span>
                                <span>{{ $mvt->date_mouvement ? $mvt->date_mouvement->format('d/m/Y') : ($mvt->created_at ? $mvt->created_at->format('d/m/Y') : '-') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PC : tableau -->
                <div class="hidden lg:block overflow-x-auto p-4">
                    <table class="w-full text-left text-xs border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-surface text-[11px] font-semibold text-content">
                                <th class="py-3 px-4 rounded-l-xl w-14">Icône</th>
                                <th class="py-3 px-4">Référence</th>
                                <th class="py-3 px-4">Opération</th>
                                <th class="py-3 px-4">Matériel</th>
                                <th class="py-3 px-4">Situation départ → arrivée</th>
                                <th class="py-3 px-4">Qté</th>
                                <th class="py-3 px-4">Motif</th>
                                <th class="py-3 px-4 rounded-r-xl">Date &amp; Opérateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                @php $mvt = $row['mvt']; $op = $row['op']; @endphp
                                <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                    <!-- Icône du produit -->
                                    <td class="py-3.5 px-4">
                                        <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center">
                                            {!! $svg($iconFor($mvt->produit)) !!}
                                        </div>
                                    </td>

                                    <!-- Référence -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-mono text-xs font-bold text-content">{{ $mvt->reference }}</span>
                                    </td>

                                    <!-- Opération -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold {{ $op[1] }}">
                                            {!! $svg($op[2], 'w-3.5 h-3.5') !!}
                                            {{ $op[0] }}
                                        </span>
                                    </td>

                                    <!-- Matériel -->
                                    <td class="py-3.5 px-4">
                                        <div class="text-xs font-semibold text-content">
                                            {{ $mvt->produit->nom ?? $mvt->produit->designation ?? 'N/A' }}
                                        </div>
                                        <div class="text-[11px] text-content-secondary mt-0.5">
                                            Réf : {{ $mvt->produit->reference ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Situation départ -> arrivée -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                        @if ($mvt->situation_depart || $mvt->situation_arrivee)
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-medium text-content">{{ $mvt->situation_depart_label ?? '—' }}</span>
                                                <span class="text-content-secondary">&rarr;</span>
                                                <span class="font-bold text-primary">{{ $mvt->situation_arrivee_label ?? '—' }}</span>
                                            </div>
                                        @else
                                            <span class="text-content-secondary">—</span>
                                        @endif
                                    </td>

                                    <!-- Quantité -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs font-bold text-content">
                                        @if ($row['isEntree'])
                                            <span class="text-emerald-600 font-bold">+{{ $mvt->quantite }}</span>
                                        @elseif ($row['isSortie'])
                                            <span class="text-rose-600 font-bold">-{{ $mvt->quantite }}</span>
                                        @else
                                            <span>{{ $mvt->quantite }}</span>
                                        @endif
                                    </td>

                                    <!-- Motif -->
                                    <td class="py-3.5 px-4 text-xs">
                                        <div class="text-content font-medium truncate max-w-xs" title="{{ $mvt->motif }}">
                                            {{ $mvt->motif ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Date & Opérateur -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                        <div class="text-content font-medium">
                                            {{ $mvt->user->name ?? 'Système' }}
                                        </div>
                                        <div class="text-[11px] text-content-secondary mt-0.5">
                                            {{ $mvt->date_mouvement ? $mvt->date_mouvement->format('d/m/Y') : ($mvt->created_at ? $mvt->created_at->format('d/m/Y') : '-') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($mouvements->hasPages())
                    <div class="p-4 border-t border-border/50">
                        {{ $mouvements->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-layouts.dashboard>