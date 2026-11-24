@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    // Si icon_name d'une catégorie n'est pas dans cette liste, l'icône « package » est utilisée.
    $icons = [
        'package'        => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'boxes'          => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'layers'         => 'm12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z|m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65|m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65',
        'check-circle'   => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'chevron-left'   => 'm15 18-6-6 6-6',
        'mail'           => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'pencil'         => 'M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z|m15 5 4 4',
        'trash-2'        => 'M3 6h18|M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6|M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2|M10 11v6|M14 11v6',
        'file-text'      => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M10 9H8|M16 13H8|M16 17H8',
        'cpu'            => 'M12 20v2|M12 2v2|M17 20v2|M17 2v2|M2 12h2|M2 17h2|M2 7h2|M20 12h2|M20 17h2|M20 7h2|M7 20v2|M7 2v2|M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z|M9 9h6v6H9z',
        'refresh-cw'     => 'M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8|M21 3v5h-5|M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16|M8 16H3v5',
        // Icônes par type de matériel (au cas où icon_name les utilise)
        'monitor'        => 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M8 21h8|M12 17v4',
        'laptop'         => 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16',
        'mouse'          => 'M12 2a7 7 0 0 0-7 7v6a7 7 0 0 0 14 0V9a7 7 0 0 0-7-7z|M12 6v4',
        'keyboard'       => 'M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z|M6 8h.01|M10 8h.01|M14 8h.01|M18 8h.01|M8 12h.01|M12 12h.01|M16 12h.01|M7 16h10',
        'printer'        => 'M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2|M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6|M8 14h8v7a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1z',
        'headphones'     => 'M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3',
        'smartphone'     => 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M12 18h.01',
        'tablet'         => 'M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M12 18h.01',
        'hard-drive'     => 'M22 12H2|M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z|M6 16h.01|M10 16h.01',
        'server'         => 'M4 2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M4 14h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2z|M6 6h.01|M6 18h.01',
        'wifi'           => 'M12 20h.01|M2 8.82a15 15 0 0 1 20 0|M5 12.859a10 10 0 0 1 14 0|M8.5 16.429a5 5 0 0 1 7 0',
        'plug'           => 'M12 22v-5|M9 8V2|M15 8V2|M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z',
        'video'          => 'm16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5|M4 6h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? $icons['package']) as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard :title="$product->nom" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), $product->nom => null]">

    <div
        x-data="{
            deleteModalOpen: false
        }"
        @keydown.escape.window="deleteModalOpen = false"
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

        <!-- En-tête -->
        <div class="mb-6 p-5 sm:p-6 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-6">
            <div class="min-w-0">
                <a
                    href="{{ route('it.products.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-surface text-content text-xs font-semibold hover:text-[#0092FD] hover:bg-slate-100 transition-colors mb-3"
                >
                    {!! $svg('chevron-left', 'w-4 h-4') !!}
                    Retour à la gestion du matériel
                </a>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-content tracking-tight break-words">{{ $product->nom }}</h1>

                    @if ($product->categorie)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-[#0092FD]/10 text-[#0092FD]">
                            {!! $svg('layers', 'w-3.5 h-3.5') !!}
                            {{ $product->categorie->nom }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-content-secondary mt-1.5">
                    Référence : <code class="font-mono font-semibold text-content px-2 py-0.5 rounded-full bg-surface break-all">{{ $product->reference }}</code>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:flex sm:items-center gap-2.5 sm:gap-3 sm:flex-wrap lg:self-center">
                @if ($product->isActif())
                    <a
                        href="{{ route('it.demandes.create', ['produit_id' => $product->id]) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#FE7A04] hover:bg-[#e66d00] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(254,122,4,0.35)] transition-colors"
                        title="Préparer une demande de réapprovisionnement par Gmail"
                    >
                        {!! $svg('mail', 'w-4 h-4') !!}
                        Demander du matériel
                    </a>
                @endif

                <a
                    href="{{ route('it.products.edit', $product) }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors"
                >
                    {!! $svg('pencil', 'w-4 h-4') !!}
                    Modifier
                </a>

                <button
                    type="button"
                    @click="deleteModalOpen = true"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-xs font-semibold text-rose-700 transition-colors cursor-pointer"
                >
                    {!! $svg('trash-2', 'w-4 h-4') !!}
                    Supprimer
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

            <!-- Colonne gauche -->
            <div class="space-y-4 sm:space-y-6">

                <!-- Photo -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)] text-center">
                    <div class="w-full h-52 sm:h-56 rounded-2xl overflow-hidden bg-surface/70 flex items-center justify-center relative mb-4">
                        @if ($product->photo_url)
                            <img
                                src="{{ $product->photo_url }}"
                                alt="{{ $product->nom }}"
                                class="w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            />
                            <div class="flex-col items-center justify-center text-content-secondary" style="display:none;">
                                <div class="w-16 h-16 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center mb-2">
                                    {!! $svg($product->categorie->icon_name ?? 'package', 'w-8 h-8') !!}
                                </div>
                                <span class="text-xs text-content-secondary/80">Photo introuvable</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center text-content-secondary">
                                <div class="w-16 h-16 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center mb-2">
                                    {!! $svg($product->categorie->icon_name ?? 'package', 'w-8 h-8') !!}
                                </div>
                                <span class="text-xs text-content-secondary/80">Aucune photo enregistrée</span>
                            </div>
                        @endif
                    </div>

                    <h2 class="text-base font-bold text-content break-words">{{ $product->nom }}</h2>
                    <p class="text-xs text-content-secondary mt-0.5">
                        {{ $product->marque ?: 'Marque non spécifiée' }}
                        @if ($product->modele)
                            &bull; {{ $product->modele }}
                        @endif
                    </p>
                </div>

                <!-- Caractéristiques de référence -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                    <h3 class="text-xs font-bold text-content uppercase tracking-wider mb-4 pb-3 border-b border-border/60">
                        Caractéristiques de référence
                    </h3>

                    <dl class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Catégorie</dt>
                            <dd class="text-right min-w-0">
                                <a
                                    href="{{ route('it.products.index', ['category_id' => $product->categorie_id]) }}"
                                    class="inline-flex items-center gap-1.5 font-semibold text-[#0092FD] hover:underline"
                                >
                                    {!! $svg($product->categorie->icon_name ?? 'layers', 'w-3.5 h-3.5') !!}
                                    {{ $product->categorie->nom ?? 'Non classé' }}
                                </a>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Référence (SKU)</dt>
                            <dd class="font-mono font-semibold text-content text-right break-all">{{ $product->reference }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Marque</dt>
                            <dd class="font-medium text-content text-right">{{ $product->marque ?: '—' }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Modèle</dt>
                            <dd class="font-medium text-content text-right">{{ $product->modele ?: '—' }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Mode de suivi</dt>
                            <dd class="font-semibold text-content text-right">{{ $product->type_suivi_label }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Prix unitaire indicatif</dt>
                            <dd class="font-bold text-content text-right">
                                @if ($product->prix_indicatif !== null)
                                    {{ number_format($product->prix_indicatif, 2, ',', ' ') }} € HT
                                @else
                                    <span class="text-content-secondary">—</span>
                                @endif
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-content-secondary">Seuil d'alerte stock</dt>
                            <dd class="font-bold text-[#FE7A04] text-right">
                                {{ $product->seuil_minimum }} unité(s)
                            </dd>
                        </div>

                        <div class="pt-3 border-t border-border/60 flex items-center justify-between gap-3 text-[11px]">
                            <dt class="text-content-secondary">Créé le</dt>
                            <dd class="text-content-secondary text-right">{{ $product->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-3 text-[11px]">
                            <dt class="text-content-secondary">Dernière modification</dt>
                            <dd class="text-content-secondary text-right">{{ $product->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>

            </div>

            <!-- Colonne droite -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">

                <!-- Description générale -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-border/60">
                        <div class="w-9 h-9 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                            {!! $svg('file-text', 'w-4 h-4') !!}
                        </div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                            Description générale
                        </h3>
                    </div>

                    @if ($product->description)
                        <div class="text-xs text-content leading-relaxed whitespace-pre-line break-words">
                            {{ $product->description }}
                        </div>
                    @else
                        <p class="text-xs text-content-secondary italic">Aucune description n'a été saisie pour ce modèle.</p>
                    @endif
                </div>

                <!-- Spécifications techniques -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-border/60">
                        <div class="w-9 h-9 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                            {!! $svg('cpu', 'w-4 h-4') !!}
                        </div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                            Spécifications techniques
                        </h3>
                    </div>

                    @if ($product->caracteristiques)
                        <pre class="text-xs font-mono text-content bg-surface/70 p-4 rounded-2xl leading-relaxed whitespace-pre-wrap break-words overflow-x-auto">{{ $product->caracteristiques }}</pre>
                    @else
                        <p class="text-xs text-content-secondary italic">Aucune caractéristique technique détaillée n'a été spécifiée.</p>
                    @endif
                </div>

                <!-- Situation & Quantités -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-border/60">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                {!! $svg('boxes', 'w-4 h-4') !!}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                                    Situation & Quantités du matériel
                                </h3>
                                <p class="text-[11px] text-content-secondary">Répartition entre Réserve, Utilisation et Panne</p>
                            </div>
                        </div>

                        <a
                            href="{{ route('it.stocks.index', ['search' => $product->nom]) }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full bg-[#0092FD]/10 hover:bg-[#0092FD] text-[#0092FD] hover:text-white text-xs font-semibold transition-colors flex-shrink-0"
                        >
                            {!! $svg('refresh-cw', 'w-3.5 h-3.5') !!}
                            Gérer le stock
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-4 rounded-2xl bg-emerald-50/70">
                            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">En stock</span>
                            <span class="text-2xl font-extrabold text-emerald-700 block mt-1">{{ $product->quantite_en_stock }}</span>
                            <span class="text-[10px] text-emerald-600">Disponible en réserve</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[#0092FD]/10">
                            <span class="text-[11px] font-bold text-[#022D77] uppercase tracking-wider block">En utilisation</span>
                            <span class="text-2xl font-extrabold text-[#022D77] block mt-1">{{ $product->quantite_en_utilisation }}</span>
                            <span class="text-[10px] text-[#022D77]/70">En production</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-rose-50/70">
                            <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider block">En panne</span>
                            <span class="text-2xl font-extrabold text-rose-600 block mt-1">{{ $product->quantite_en_panne }}</span>
                            <span class="text-[10px] text-rose-600">Hors service</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-surface">
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Total global</span>
                            <span class="text-2xl font-extrabold text-content block mt-1">{{ $product->total_stock }}</span>
                            <span class="text-[10px] text-content-secondary">Total des unités</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ========================================================= -->
        <!-- MODALE : SUPPRESSION DE LA RÉFÉRENCE                      -->
        <!-- ========================================================= -->
        <div
            x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>

                <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-5 sm:p-6 z-10">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            {!! $svg('trash-2', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-content">Supprimer la référence</h3>
                            <p class="text-xs text-content-secondary mt-1 break-words">
                                Êtes-vous sûr de vouloir supprimer définitivement le produit
                                <strong class="text-content">« {{ $product->nom }} »</strong> (SKU: {{ $product->reference }}) ?
                            </p>
                            <p class="text-[11px] text-content-secondary mt-2">
                                Cette action supprimera définitivement le produit ainsi que le visuel associé.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-border/60 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                        <button
                            type="button"
                            @click="deleteModalOpen = false"
                            class="px-5 py-2.5 bg-surface hover:bg-slate-100 rounded-full text-xs font-semibold text-content transition-colors cursor-pointer"
                        >
                            Annuler
                        </button>
                        <form action="{{ route('it.products.destroy', $product) }}" method="POST" class="flex">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="w-full px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-full transition-colors cursor-pointer"
                            >
                                Confirmer la suppression
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layouts.dashboard>