@php
    // Icônes SVG intégrées (style Lucide) : plus besoin de x-ui.icon, chaque icône s'affiche toujours correctement.
    $icons = [
        'plus'              => 'M5 12h14|M12 5v14',
        'package'           => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'package-plus'      => 'M16 16h6|M19 13v6|M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14|m7.5 4.27 9 5.15|M3.29 7 12 12l8.71-5|M12 22V12',
        'package-x'         => 'M21 10.656V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14|m7.5 4.27 9 5.15|M3.29 7 12 12l8.71-5|M12 22V12|m17 13 5 5m-5 0 5-5',
        'boxes'             => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'laptop'            => 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16',
        'alert-octagon'     => 'M12 16h.01|M12 8v4|M15.312 2a2 2 0 0 1 1.414.586l4.688 4.688A2 2 0 0 1 22 8.688v6.624a2 2 0 0 1-.586 1.414l-4.688 4.688a2 2 0 0 1-1.414.586H8.688a2 2 0 0 1-1.414-.586l-4.688-4.688A2 2 0 0 1 2 15.312V8.688a2 2 0 0 1 .586-1.414l4.688-4.688A2 2 0 0 1 8.688 2z',
        'alert-triangle'    => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'arrow-up-right'    => 'M7 7h10v10|M7 17 17 7',
        'arrow-down-to-line'=> 'M12 17V3|m6 11 6 6 6-6|M19 21H5',
        'arrow-up-from-line'=> 'm18 9-6-6-6 6|M12 3v14|M5 21h14',
        'bell'              => 'M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9|M10.3 21a1.94 1.94 0 0 0 3.4 0',
        'check-circle'      => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'mail'              => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'clipboard-list'    => 'M9 2h6a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z|M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2|M12 11h4|M12 16h4|M8 11h.01|M8 16h.01',
        'history'           => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8|M3 3v5h5|M12 7v5l4 2',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Tableau de bord" :breadcrumbs="['Tableau de bord' => null]">

    <!-- 1. Barre d'actions -->
    <div class="mb-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
        <a
            href="{{ route('it.stocks.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] transition-all"
        >
            {!! $svg('boxes', 'w-4 h-4 text-primary') !!}
            Gestion du stock
        </a>
        <a
            href="{{ route('it.products.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors"
        >
            {!! $svg('plus', 'w-4 h-4') !!}
            Ajouter un matériel
        </a>
    </div>

    <!-- 2. Cartes statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <!-- Produits enregistrés -->
        <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                        {!! $svg('package') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Produits enregistrés</p>
                </div>
                <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                    {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                </span>
            </div>
            <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $totalProduits }}</p>
            <p class="text-xs text-content-secondary mt-1">Références actives</p>
        </div>

        <!-- Unités en stock -->
        <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        {!! $svg('boxes') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Unités en stock</p>
                </div>
                <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                    {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                </span>
            </div>
            <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($totalEnStock) }}</p>
            <p class="text-xs text-content-secondary mt-1">Disponibles en réserve</p>
        </div>

        <!-- Unités en utilisation -->
        <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                        {!! $svg('laptop') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Unités en utilisation</p>
                </div>
                <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                    {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                </span>
            </div>
            <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($totalEnUtilisation) }}</p>
            <p class="text-xs text-content-secondary mt-1">Bureaux et production</p>
        </div>

        <!-- Unités en panne -->
        <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full {{ $totalEnPanne > 0 ? 'bg-rose-50 text-rose-600' : 'bg-surface text-content-secondary' }} flex items-center justify-center flex-shrink-0">
                        {!! $svg('alert-octagon') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Unités en panne</p>
                </div>
                <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                    {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                </span>
            </div>
            <p class="mt-4 text-2xl sm:text-3xl font-extrabold {{ $totalEnPanne > 0 ? 'text-rose-600' : 'text-content' }} tracking-tight">{{ number_format($totalEnPanne) }}</p>
            <p class="text-xs text-content-secondary mt-1">Hors service / à réparer</p>
        </div>
    </div>

    <!-- 3. Alertes de stock -->
    <div class="mb-6 bg-white rounded-3xl p-4 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-9 h-9 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] flex items-center justify-center flex-shrink-0">
                {!! $svg('bell', 'w-4 h-4') !!}
            </div>
            <h3 class="text-sm font-bold text-content">Alertes de stock</h3>
            @if (!empty($alertesStock) && count($alertesStock) > 0)
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FE7A04]/10 text-[#FE7A04]">
                    {{ count($alertesStock) }}
                </span>
            @endif
        </div>

        @if (empty($alertesStock) || count($alertesStock) === 0)
            <div class="p-4 bg-emerald-50/60 rounded-2xl text-xs text-emerald-700 flex items-center gap-2.5">
                {!! $svg('check-circle', 'w-4 h-4 flex-shrink-0') !!}
                <span>Aucun stock faible ou épuisé.</span>
            </div>
        @else
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
                @foreach ($alertesStock as $alerte)
                    @php
                        $prod = $alerte['produit'];
                        $isRupture = ($alerte['type'] === 'rupture');
                    @endphp
                    <div class="p-4 rounded-2xl bg-surface/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $isRupture ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                                {!! $svg($isRupture ? 'package-x' : 'alert-triangle') !!}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold text-content break-words">{{ $prod->nom }}</h4>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $isRupture ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $isRupture ? 'Épuisé' : 'Stock faible' }}
                                    </span>
                                </div>
                                <p class="text-xs mt-0.5 text-content-secondary">
                                    @if ($isRupture)
                                        Ce produit est épuisé.
                                    @else
                                        Il reste 1 unité en stock.
                                    @endif
                                    @if ($prod->reference)
                                        <span class="text-content-secondary/60">&bull;</span>
                                        <span class="font-mono text-[11px]">Réf : {{ $prod->reference }}</span>
                                    @endif
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
                            Demander
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 4. Actions rapides -->
    <div class="mb-6">
        <h3 class="text-sm font-bold text-content mb-3">Actions rapides</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
            <!-- Ajouter un matériel -->
            <a href="{{ route('it.products.create') }}"
               class="group p-4 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] hover:-translate-y-0.5 transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] group-hover:bg-[#0092FD] group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    {!! $svg('package-plus') !!}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content">Ajouter un matériel</h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Nouveau produit</p>
                </div>
            </a>

            <!-- Entrée de stock -->
            <a href="{{ route('it.stocks.entree') }}"
               class="group p-4 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] hover:-translate-y-0.5 transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    {!! $svg('arrow-down-to-line') !!}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content">Entrée de stock</h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Ajouter du stock</p>
                </div>
            </a>

            <!-- Sortie de stock -->
            <a href="{{ route('it.stocks.sortie') }}"
               class="group p-4 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] hover:-translate-y-0.5 transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-full bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    {!! $svg('arrow-up-from-line') !!}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content">Sortie de stock</h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Retirer du stock</p>
                </div>
            </a>

            <!-- Nouvelle demande -->
            <a href="{{ route('it.demandes.create') }}"
               class="group p-4 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] hover:-translate-y-0.5 transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] group-hover:bg-[#FE7A04] group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    {!! $svg('clipboard-list') !!}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content">Nouvelle demande</h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Demander du matériel</p>
                </div>
            </a>

            <!-- Voir l'historique -->
            <a href="{{ route('it.mouvements.index') }}"
               class="group p-4 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] hover:shadow-[0_8px_24px_rgba(2,45,119,0.12)] hover:-translate-y-0.5 transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-full bg-[#022D77]/10 text-[#022D77] group-hover:bg-[#022D77] group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    {!! $svg('history') !!}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content">Voir l'historique</h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Journal des mouvements</p>
                </div>
            </a>
        </div>
    </div>

    <!-- 5. Derniers mouvements -->
    <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-4 sm:p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-content">Derniers mouvements</h3>
                <p class="text-xs text-content-secondary mt-0.5">Historique des 5 dernières opérations enregistrées</p>
            </div>
            <a href="{{ route('it.mouvements.index') }}" class="text-xs font-semibold text-[#0092FD] hover:underline inline-flex items-center gap-1">
                Voir l'historique &rarr;
            </a>
        </div>

        @if (empty($recentMovements) || $recentMovements->isEmpty())
            <div class="py-8 text-center text-xs text-content-secondary">
                {!! $svg('history', 'w-8 h-8 mx-auto text-content-secondary/40 mb-2') !!}
                Aucun mouvement récent.
            </div>
        @else
            <!-- Mobile : cartes empilées -->
            <div class="md:hidden space-y-3">
                @foreach ($recentMovements as $m)
                    <div class="p-4 rounded-2xl bg-surface/60">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-content break-words">{{ $m->produit->nom ?? '-' }}</p>
                                @if ($m->produit->reference)
                                    <p class="text-[10px] text-content-secondary font-mono">{{ $m->produit->reference }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                @if ($m->type === 'entree')
                                    <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-emerald-50 text-emerald-700">Entrée</span>
                                    <span class="text-sm font-bold text-emerald-600">+{{ $m->quantite }}</span>
                                @elseif ($m->type === 'sortie')
                                    <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-rose-50 text-rose-700">Sortie</span>
                                    <span class="text-sm font-bold text-rose-600">-{{ $m->quantite }}</span>
                                @elseif ($m->type === 'transfert')
                                    <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-blue-50 text-blue-700">Transfert</span>
                                    <span class="text-sm font-bold text-content">{{ $m->quantite }}</span>
                                @else
                                    <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-amber-50 text-amber-700">Correction</span>
                                    <span class="text-sm font-bold text-content">{{ $m->quantite }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-border/60 grid grid-cols-2 gap-2 text-[11px] text-content-secondary">
                            <div class="col-span-2">
                                @if ($m->situation_depart_label && $m->situation_arrivee_label)
                                    <span class="font-medium text-content">{{ $m->situation_depart_label }}</span> &rarr; <span class="font-medium text-content">{{ $m->situation_arrivee_label }}</span>
                                @elseif ($m->situation_arrivee_label)
                                    <span class="font-medium text-emerald-700">{{ $m->situation_arrivee_label }}</span>
                                @elseif ($m->situation_depart_label)
                                    <span class="font-medium text-rose-700">{{ $m->situation_depart_label }}</span>
                                @else
                                    -
                                @endif
                            </div>
                            <span>{{ $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-' }}</span>
                            <span class="text-right text-content">{{ $m->user->name ?? 'Système' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tablette / PC : tableau -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-surface text-[11px] font-semibold text-content">
                            <th class="py-3 px-4 rounded-l-xl">Matériel</th>
                            <th class="py-3 px-4">Mouvement</th>
                            <th class="py-3 px-4 text-right">Quantité</th>
                            <th class="py-3 px-4">Situation</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 rounded-r-xl">Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentMovements as $m)
                            <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                <td class="py-3.5 px-4 font-semibold text-content">
                                    {{ $m->produit->nom ?? '-' }}
                                    @if ($m->produit->reference)
                                        <span class="block text-[10px] text-content-secondary font-mono font-normal">{{ $m->produit->reference }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($m->type === 'entree')
                                        <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-emerald-50 text-emerald-700">Entrée</span>
                                    @elseif ($m->type === 'sortie')
                                        <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-rose-50 text-rose-700">Sortie</span>
                                    @elseif ($m->type === 'transfert')
                                        <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-blue-50 text-blue-700">Transfert</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full font-semibold text-[10px] bg-amber-50 text-amber-700">Correction</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-content">
                                    @if ($m->type === 'entree')
                                        <span class="text-emerald-600">+{{ $m->quantite }}</span>
                                    @elseif ($m->type === 'sortie')
                                        <span class="text-rose-600">-{{ $m->quantite }}</span>
                                    @else
                                        {{ $m->quantite }}
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-content-secondary text-[11px]">
                                    @if ($m->situation_depart_label && $m->situation_arrivee_label)
                                        <span class="font-medium text-content">{{ $m->situation_depart_label }}</span> &rarr; <span class="font-medium text-content">{{ $m->situation_arrivee_label }}</span>
                                    @elseif ($m->situation_arrivee_label)
                                        <span class="font-medium text-emerald-700">{{ $m->situation_arrivee_label }}</span>
                                    @elseif ($m->situation_depart_label)
                                        <span class="font-medium text-rose-700">{{ $m->situation_depart_label }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-content-secondary whitespace-nowrap">
                                    {{ $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-content whitespace-nowrap">
                                    {{ $m->user->name ?? 'Système' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.dashboard>