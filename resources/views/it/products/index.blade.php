@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'plus'           => 'M5 12h14|M12 5v14',
        'package'        => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'boxes'          => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'laptop'         => 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16',
        'alert-octagon'  => 'M12 16h.01|M12 8v4|M15.312 2a2 2 0 0 1 1.414.586l4.688 4.688A2 2 0 0 1 22 8.688v6.624a2 2 0 0 1-.586 1.414l-4.688 4.688a2 2 0 0 1-1.414.586H8.688a2 2 0 0 1-1.414-.586l-4.688-4.688A2 2 0 0 1 2 15.312V8.688a2 2 0 0 1 .586-1.414l4.688-4.688A2 2 0 0 1 8.688 2z',
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'check-circle'   => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'arrow-up-right' => 'M7 7h10v10|M7 17 17 7',
        'layers'         => 'm12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z|m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65|m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65',
        'search'         => 'm21 21-4.34-4.34|M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16z',
        'filter'         => 'M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z',
        'refresh-cw'     => 'M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8|M21 3v5h-5|M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16|M8 16H3v5',
        'eye'            => 'M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0|M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
        'pencil'         => 'M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z|m15 5 4 4',
        // Icônes par type de matériel
        'monitor'        => 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M8 21h8|M12 17v4',
        'cpu'            => 'M12 20v2|M12 2v2|M17 20v2|M17 2v2|M2 12h2|M2 17h2|M2 7h2|M20 12h2|M20 17h2|M20 7h2|M7 20v2|M7 2v2|M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z|M9 9h6v6H9z',
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

    // Choisit l'icône selon le nom du produit + sa catégorie (le premier mot-clé trouvé gagne).
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
    // 1) le nom du produit décide ; 2) sinon la catégorie ; 3) sinon icône colis.
    $iconFor = function ($p) use ($matchIcon) {
        return $matchIcon($p->nom ?? '')
            ?? $matchIcon($p->categorie->nom ?? '')
            ?? 'package';
    };
@endphp

<x-layouts.dashboard title="Gestion du matériel" :breadcrumbs="['Gestion du matériel' => null]">

    <div>
        <!-- Messages flash -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                {!! $svg('check-circle', 'w-5 h-5 text-emerald-600 flex-shrink-0') !!}
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 bg-red-50 rounded-2xl flex items-center gap-3 text-red-800 text-sm">
                {!! $svg('alert-triangle', 'w-5 h-5 text-red-600 flex-shrink-0') !!}
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Barre d'actions -->
        <div class="mb-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
            <a
                href="{{ route('it.categories.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] transition-all"
            >
                {!! $svg('layers', 'w-4 h-4 text-primary') !!}
                Gérer les catégories
            </a>
            <a
                href="{{ route('it.products.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors"
            >
                {!! $svg('plus', 'w-4 h-4') !!}
                Ajouter un matériel
            </a>
        </div>

        <!-- 4 cartes statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
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
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $totalProducts }}</p>
                <p class="text-xs text-content-secondary mt-1">Références actives</p>
            </div>

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
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($totalEnStock) }}</p>
                <p class="text-xs text-content-secondary mt-1">Disponibles en réserve</p>
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
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ number_format($totalEnUtilisation) }}</p>
                <p class="text-xs text-content-secondary mt-1">Bureaux et production</p>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full {{ $totalEnPanne > 0 ? 'bg-rose-50 text-rose-600' : 'bg-surface text-content-secondary' }} flex items-center justify-center flex-shrink-0">
                            {!! $svg('alert-octagon') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">En panne</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold {{ $totalEnPanne > 0 ? 'text-rose-600' : 'text-content' }} tracking-tight">{{ number_format($totalEnPanne) }}</p>
                <p class="text-xs text-content-secondary mt-1">Hors service / à réparer</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 mb-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <form method="GET" action="{{ route('it.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Recherche -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-secondary">
                        {!! $svg('search', 'w-4 h-4') !!}
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Recherche par nom, référence..."
                        class="block w-full pl-10 pr-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    />
                </div>

                <!-- Catégorie -->
                <div>
                    <select
                        name="category_id"
                        onchange="this.form.submit()"
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Toutes les catégories</option>
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
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Toutes les situations</option>
                        <option value="en_stock" {{ $situation === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ $situation === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ $situation === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- Niveau du stock -->
                <div>
                    <select
                        name="etat_stock"
                        onchange="this.form.submit()"
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Tous les niveaux</option>
                        <option value="disponible" {{ $etatStock === 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="faible" {{ $etatStock === 'faible' ? 'selected' : '' }}>Stock faible</option>
                        <option value="rupture" {{ $etatStock === 'rupture' ? 'selected' : '' }}>Épuisé</option>
                    </select>
                </div>

                <!-- Filtrer & Réinitialiser -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold rounded-full transition-colors cursor-pointer"
                    >
                        {!! $svg('filter', 'w-4 h-4') !!}
                        Filtrer
                    </button>

                    @if ($search || $categoryId || $situation || $etatStock)
                        <a
                            href="{{ route('it.products.index') }}"
                            class="inline-flex items-center justify-center p-2.5 bg-surface text-content-secondary hover:text-content rounded-full transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            {!! $svg('refresh-cw', 'w-4 h-4') !!}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste des matériels -->
        <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] overflow-hidden">
            @if ($products->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        {!! $svg('package', 'w-8 h-8') !!}
                    </div>
                    <h3 class="text-base font-bold text-content">Aucun matériel trouvé</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">
                        @if ($search || $categoryId || $situation || $etatStock)
                            Aucun produit ne correspond à vos critères de recherche.
                        @else
                            Votre catalogue de matériel informatique est actuellement vide.
                        @endif
                    </p>
                    <div class="mt-5">
                        @if ($search || $categoryId || $situation || $etatStock)
                            <a href="{{ route('it.products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-surface text-content text-xs font-semibold rounded-full hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
                            </a>
                        @else
                            <a
                                href="{{ route('it.products.create') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold rounded-full transition-colors"
                            >
                                {!! $svg('plus', 'w-4 h-4') !!}
                                Ajouter un matériel
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Mobile / tablette : cartes -->
                <div class="lg:hidden p-4 space-y-3">
                    @foreach ($products as $p)
                        @php
                            $enStock = $p->quantite_en_stock;
                            $enUtil = $p->quantite_en_utilisation;
                            $enPanne = $p->quantite_en_panne;
                            $total = $p->total_stock;
                            $ico = $iconFor($p);
                        @endphp
                        <div class="p-4 rounded-2xl bg-surface/60">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if ($p->photo_url)
                                        <img src="{{ $p->photo_url }}" alt="{{ $p->nom }}" class="w-full h-full object-cover" onerror="this.style.display='none';" />
                                    @else
                                        {!! $svg($ico) !!}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('it.products.show', $p) }}" class="text-sm font-bold text-content hover:text-[#0092FD] transition-colors break-words block">
                                        {{ $p->nom }}
                                    </a>
                                    <div class="flex items-center gap-2 flex-wrap text-[11px] text-content-secondary mt-0.5">
                                        @if ($p->marque)
                                            <span>{{ $p->marque }}</span>
                                        @endif
                                        @if ($p->reference)
                                            @if ($p->marque)<span>&bull;</span>@endif
                                            <span class="font-mono">Réf : {{ $p->reference }}</span>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-[11px] font-medium bg-white text-content">
                                        {{ $p->categorie->nom ?? 'Non classé' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <a href="{{ route('it.products.show', $p) }}" class="p-2 text-content-secondary hover:text-[#0092FD] hover:bg-white rounded-full transition-colors" title="Consulter la fiche">
                                        {!! $svg('eye', 'w-4 h-4') !!}
                                    </a>
                                    <a href="{{ route('it.products.edit', $p) }}" class="p-2 text-content-secondary hover:text-[#0092FD] hover:bg-white rounded-full transition-colors" title="Modifier">
                                        {!! $svg('pencil', 'w-4 h-4') !!}
                                    </a>
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
                                <th class="py-3 px-4 text-center">Total</th>
                                <th class="py-3 px-5 text-right rounded-r-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                @php
                                    $enStock = $p->quantite_en_stock;
                                    $enUtil = $p->quantite_en_utilisation;
                                    $enPanne = $p->quantite_en_panne;
                                    $total = $p->total_stock;
                                    $status = $p->stock_status; // rupture, faible, normal
                                    $ico = $iconFor($p);
                                @endphp
                                <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                    <!-- Matériel -->
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if ($p->photo_url)
                                                    <img src="{{ $p->photo_url }}" alt="{{ $p->nom }}" class="w-full h-full object-cover" onerror="this.style.display='none';" />
                                                @else
                                                    {!! $svg($ico, 'w-5 h-5') !!}
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('it.products.show', $p) }}" class="font-bold text-content hover:text-[#0092FD] transition-colors truncate block">
                                                    {{ $p->nom }}
                                                </a>
                                                <div class="flex items-center gap-2 text-[11px] text-content-secondary mt-0.5">
                                                    @if ($p->marque)
                                                        <span>{{ $p->marque }}</span>
                                                    @endif
                                                    @if ($p->reference)
                                                        <span>&bull;</span>
                                                        <span class="font-mono text-content-secondary/80">Réf : {{ $p->reference }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 text-content">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-surface text-content">
                                            {{ $p->categorie->nom ?? 'Non classé' }}
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

                                    <td class="py-3.5 px-5 text-right">
                                        <div class="inline-flex items-center gap-1 justify-end">
                                            <a
                                                href="{{ route('it.products.show', $p) }}"
                                                class="p-2 text-content-secondary hover:text-[#0092FD] hover:bg-surface rounded-full transition-colors"
                                                title="Consulter la fiche"
                                            >
                                                {!! $svg('eye', 'w-4 h-4') !!}
                                            </a>
                                            <a
                                                href="{{ route('it.products.edit', $p) }}"
                                                class="p-2 text-content-secondary hover:text-[#0092FD] hover:bg-surface rounded-full transition-colors"
                                                title="Modifier"
                                            >
                                                {!! $svg('pencil', 'w-4 h-4') !!}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-border/50">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.dashboard>