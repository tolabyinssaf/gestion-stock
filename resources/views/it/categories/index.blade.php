@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'plus'          => 'M5 12h14|M12 5v14',
        'x'             => 'M18 6 6 18|m6 6 12 12',
        'search'        => 'm21 21-4.34-4.34|M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16z',
        'filter'        => 'M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z',
        'refresh-cw'    => 'M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8|M21 3v5h-5|M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16|M8 16H3v5',
        'check-circle'  => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'alert-triangle'=> 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'arrow-left'    => 'm12 19-7-7 7-7|M19 12H5',
        'pencil'        => 'M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z|m15 5 4 4',
        'trash-2'       => 'M3 6h18|M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6|M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2|M10 11v6|M14 11v6',
        'archive'       => 'M4 3h16a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8|M10 12h4',
        'layers'        => 'm12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z|m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65|m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65',
        'package'       => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        // Icônes sélectionnables pour une catégorie
        'laptop'        => 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16',
        'monitor'       => 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M8 21h8|M12 17v4',
        'cpu'           => 'M12 20v2|M12 2v2|M17 20v2|M17 2v2|M2 12h2|M2 17h2|M2 7h2|M20 12h2|M20 17h2|M20 7h2|M7 20v2|M7 2v2|M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z|M9 9h6v6H9z',
        'server'        => 'M4 2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M4 14h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2z|M6 6h.01|M6 18h.01',
        'printer'       => 'M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2|M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6|M8 14h8v7a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1z',
        'smartphone'    => 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M12 18h.01',
        'tablet'        => 'M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z|M12 18h.01',
        'hard-drive'    => 'M22 12H2|M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z|M6 16h.01|M10 16h.01',
        'wifi'          => 'M12 20h.01|M2 8.82a15 15 0 0 1 20 0|M5 12.859a10 10 0 0 1 14 0|M8.5 16.429a5 5 0 0 1 7 0',
        'cable'         => 'M17 21v-2a1 1 0 0 1-1-1v-1a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1a1 1 0 0 1-1 1|M19 15V6.5a1 1 0 0 0-7 0v11a1 1 0 0 1-7 0V9|M21 21v-2h-4|M3 5h4V3|M7 5a1 1 0 0 1 1 1v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1V3',
        'headphones'    => 'M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3',
        'wrench'        => 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z',
        'boxes'         => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? $icons['layers']) as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Catégories de matériel" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), 'Catégories' => null]">

    <div
        x-data="{
            // Drawer state (Create / Edit)
            drawerOpen: false,
            isEditing: false,
            drawerTitle: 'Ajouter une catégorie',
            formAction: '{{ route('it.categories.store') }}',
            formData: {
                id: null,
                nom: '',
                icon_name: 'laptop',
                statut: 'actif',
                description: ''
            },
            openCreateDrawer() {
                this.isEditing = false;
                this.drawerTitle = 'Ajouter une catégorie';
                this.formAction = '{{ route('it.categories.store') }}';
                this.formData = {
                    id: null,
                    nom: '',
                    icon_name: 'laptop',
                    statut: 'actif',
                    description: ''
                };
                this.drawerOpen = true;
            },
            openEditDrawer(category) {
                this.isEditing = true;
                this.drawerTitle = 'Modifier la catégorie';
                this.formAction = '/it/categories/' + category.id;
                this.formData = {
                    id: category.id,
                    nom: category.nom,
                    icon_name: category.icon_name || 'laptop',
                    statut: category.statut,
                    description: category.description || ''
                };
                this.drawerOpen = true;
            },

            // Delete Confirm Modal
            deleteModalOpen: false,
            categoryToDelete: { id: null, nom: '', count: 0 },
            openDeleteModal(category) {
                this.categoryToDelete = category;
                this.deleteModalOpen = true;
            }
        }"
        @keydown.escape.window="drawerOpen = false; deleteModalOpen = false"
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

        @if ($errors->any())
            <div class="mb-5 p-4 bg-rose-50 rounded-2xl text-rose-800 text-sm">
                <div class="flex items-center gap-2 mb-2 font-semibold">
                    {!! $svg('alert-triangle', 'w-5 h-5 text-rose-600 flex-shrink-0') !!}
                    <span>Des erreurs sont survenues lors de la validation :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Barre d'actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-xs sm:text-sm text-content-secondary max-w-xl">
                Organisez vos produits par catégorie pour faciliter la gestion du stock.
            </p>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3">
                <a
                    href="{{ route('it.products.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] transition-all"
                >
                    {!! $svg('arrow-left', 'w-4 h-4 text-primary') !!}
                    Gestion du matériel
                </a>
                <button
                    type="button"
                    @click="openCreateDrawer()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors cursor-pointer"
                >
                    {!! $svg('plus', 'w-4 h-4') !!}
                    Nouvelle catégorie
                </button>
            </div>
        </div>

        <!-- 3 cartes statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                        {!! $svg('layers') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Total catégories</p>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $totalCategories }}</p>
                <p class="text-xs text-content-secondary mt-1">Typologies de matériel</p>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        {!! $svg('check-circle') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Catégories actives</p>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $activeCategories }}</p>
                <p class="text-xs text-content-secondary mt-1">Disponibles pour ajout</p>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                        {!! $svg('archive') !!}
                    </div>
                    <p class="text-sm font-semibold text-content truncate">Catégories archivées</p>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $archivedCategories }}</p>
                <p class="text-xs text-content-secondary mt-1">Masquées du catalogue neuf</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 mb-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <form method="GET" action="{{ route('it.categories.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Recherche -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-secondary">
                        {!! $svg('search', 'w-4 h-4') !!}
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Rechercher par nom ou description..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    />
                </div>

                <!-- Statut -->
                <div>
                    <select
                        name="statut"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ $statut === 'actif' ? 'selected' : '' }}>Actives uniquement</option>
                        <option value="archive" {{ $statut === 'archive' ? 'selected' : '' }}>Archivées uniquement</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white rounded-full text-xs font-semibold transition-colors cursor-pointer"
                    >
                        {!! $svg('filter', 'w-4 h-4') !!}
                        Filtrer
                    </button>

                    @if ($search || $statut)
                        <a
                            href="{{ route('it.categories.index') }}"
                            class="p-2.5 bg-surface hover:bg-slate-100 text-content-secondary rounded-full transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            {!! $svg('refresh-cw', 'w-4 h-4') !!}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste des catégories -->
        <div class="mb-10 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] overflow-hidden">
            @if ($categories->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        {!! $svg('layers', 'w-8 h-8') !!}
                    </div>
                    <h3 class="text-base font-bold text-content">Aucune catégorie trouvée</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">
                        @if ($search || $statut)
                            Aucun élément ne correspond aux filtres appliqués. Essayez d'ajuster ou de réinitialiser la recherche.
                        @else
                            Le catalogue ne contient encore aucune catégorie. Commencez par en créer une nouvelle.
                        @endif
                    </p>
                    <div class="mt-5">
                        @if ($search || $statut)
                            <a href="{{ route('it.categories.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-surface text-content text-xs font-semibold rounded-full hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
                            </a>
                        @else
                            <button
                                type="button"
                                @click="openCreateDrawer()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold rounded-full transition-colors cursor-pointer"
                            >
                                {!! $svg('plus', 'w-4 h-4') !!}
                                Créer une catégorie
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <!-- Mobile / tablette : cartes -->
                <div class="lg:hidden p-4 space-y-3">
                    @foreach ($categories as $cat)
                        <div class="p-4 rounded-2xl bg-surface/60">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                    {!! $svg($cat->icon_name ?: 'layers') !!}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-content break-words">{{ $cat->nom }}</p>
                                    <p class="text-[11px] text-content-secondary mt-0.5">Créée le {{ $cat->created_at->format('d/m/Y') }}</p>
                                    <code class="inline-block mt-1 px-2 py-0.5 rounded-full bg-white text-[10px] font-mono text-content-secondary">{{ $cat->slug }}</code>
                                </div>
                                @if ($cat->isActif())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Archivée
                                    </span>
                                @endif
                            </div>

                            @if ($cat->description)
                                <p class="mt-3 text-xs text-content-secondary break-words">{{ $cat->description }}</p>
                            @endif

                            <div class="mt-3 pt-3 border-t border-border/60 flex items-center justify-between gap-2">
                                <a
                                    href="{{ route('it.products.index', ['category_id' => $cat->id]) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white hover:bg-[#0092FD]/10 text-content-secondary hover:text-[#0092FD] transition-colors text-xs font-semibold"
                                    title="Filtrer les produits de cette catégorie"
                                >
                                    {!! $svg('package', 'w-3.5 h-3.5') !!}
                                    {{ $cat->produits_count }} produit(s)
                                </a>

                                <div class="inline-flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click='openEditDrawer(@json($cat))'
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#0092FD]/10 text-[#0092FD] hover:bg-[#0092FD] hover:text-white transition-colors active:scale-95 cursor-pointer"
                                        title="Modifier la catégorie"
                                    >
                                        {!! $svg('pencil', 'w-4 h-4') !!}
                                    </button>

                                    <form method="POST" action="{{ route('it.categories.toggle-status', $cat) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] hover:bg-[#FE7A04] hover:text-white transition-colors active:scale-95 cursor-pointer"
                                            title="{{ $cat->isActif() ? 'Archiver la catégorie' : 'Réactiver la catégorie' }}"
                                        >
                                            {!! $svg('archive', 'w-4 h-4') !!}
                                        </button>
                                    </form>

                                    @if ($cat->produits_count === 0)
                                        <button
                                            type="button"
                                            @click='openDeleteModal({ id: {{ $cat->id }}, nom: "{{ addslashes($cat->nom) }}", count: {{ $cat->produits_count }} })'
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors active:scale-95 cursor-pointer"
                                            title="Supprimer la catégorie"
                                        >
                                            {!! $svg('trash-2', 'w-4 h-4') !!}
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            disabled
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white text-slate-300 cursor-not-allowed"
                                            title="Suppression impossible : contient des produits"
                                        >
                                            {!! $svg('trash-2', 'w-4 h-4') !!}
                                        </button>
                                    @endif
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
                                <th class="py-3 px-5 rounded-l-xl">Catégorie</th>
                                <th class="py-3 px-4">Identifiant (Slug)</th>
                                <th class="py-3 px-4">Description</th>
                                <th class="py-3 px-4 text-center">Produits</th>
                                <th class="py-3 px-4 text-center">Statut</th>
                                <th class="py-3 px-5 text-right rounded-r-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $cat)
                                <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                    <!-- Catégorie -->
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                                {!! $svg($cat->icon_name ?: 'layers') !!}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-content block text-sm">{{ $cat->nom }}</span>
                                                <span class="text-[11px] text-content-secondary">Créée le {{ $cat->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Slug -->
                                    <td class="py-3.5 px-4">
                                        <code class="px-2.5 py-1 rounded-full bg-surface text-[11px] font-mono text-content-secondary">
                                            {{ $cat->slug }}
                                        </code>
                                    </td>

                                    <!-- Description -->
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <span class="text-content-secondary line-clamp-2">
                                            {{ $cat->description ?: '—' }}
                                        </span>
                                    </td>

                                    <!-- Produits -->
                                    <td class="py-3.5 px-4 text-center">
                                        <a
                                            href="{{ route('it.products.index', ['category_id' => $cat->id]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface hover:bg-[#0092FD]/10 text-content-secondary hover:text-[#0092FD] transition-colors text-xs font-bold"
                                            title="Filtrer les produits de cette catégorie"
                                        >
                                            {!! $svg('package', 'w-3.5 h-3.5') !!}
                                            {{ $cat->produits_count }}
                                        </a>
                                    </td>

                                    <!-- Statut -->
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($cat->isActif())
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Archivée
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Modifier -->
                                            <button
                                                type="button"
                                                @click='openEditDrawer(@json($cat))'
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#0092FD]/10 text-[#0092FD] hover:bg-[#0092FD] hover:text-white transition-colors active:scale-95 cursor-pointer"
                                                title="Modifier la catégorie"
                                            >
                                                {!! $svg('pencil', 'w-4 h-4') !!}
                                            </button>

                                            <!-- Archiver / Réactiver -->
                                            <form method="POST" action="{{ route('it.categories.toggle-status', $cat) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] hover:bg-[#FE7A04] hover:text-white transition-colors active:scale-95 cursor-pointer"
                                                    title="{{ $cat->isActif() ? 'Archiver la catégorie' : 'Réactiver la catégorie' }}"
                                                >
                                                    {!! $svg('archive', 'w-4 h-4') !!}
                                                </button>
                                            </form>

                                            <!-- Supprimer -->
                                            @if ($cat->produits_count === 0)
                                                <button
                                                    type="button"
                                                    @click='openDeleteModal({ id: {{ $cat->id }}, nom: "{{ addslashes($cat->nom) }}", count: {{ $cat->produits_count }} })'
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors active:scale-95 cursor-pointer"
                                                    title="Supprimer la catégorie"
                                                >
                                                    {!! $svg('trash-2', 'w-4 h-4') !!}
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    disabled
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface text-slate-300 cursor-not-allowed"
                                                    title="Suppression impossible : contient des produits"
                                                >
                                                    {!! $svg('trash-2', 'w-4 h-4') !!}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="p-4 border-t border-border/50">
                        {{ $categories->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Drawer Création / Modification -->
        <div
            x-show="drawerOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-hidden"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="drawerOpen = false"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
                <div
                    x-show="drawerOpen"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-md bg-white sm:rounded-l-3xl shadow-2xl flex flex-col"
                >
                    <!-- En-tête du drawer -->
                    <div class="p-5 sm:p-6 border-b border-border/60 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                {!! $svg('layers') !!}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-content" x-text="drawerTitle"></h3>
                                <p class="text-xs text-content-secondary" x-text="isEditing ? 'Mettre à jour les informations' : 'Définir une nouvelle famille de matériel'"></p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="drawerOpen = false"
                            class="w-9 h-9 flex items-center justify-center rounded-full text-content-secondary hover:text-content hover:bg-surface transition-colors flex-shrink-0"
                        >
                            {!! $svg('x', 'w-5 h-5') !!}
                        </button>
                    </div>

                    <!-- Formulaire du drawer -->
                    <form :action="formAction" method="POST" class="flex-1 flex flex-col justify-between overflow-y-auto">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Nom -->
                            <div>
                                <label for="category_nom" class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                    Nom de la catégorie <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="category_nom"
                                    name="nom"
                                    x-model="formData.nom"
                                    required
                                    placeholder="Ex: Ordinateurs portables, Écrans..."
                                    class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                                />
                            </div>

                            <!-- Icône -->
                            <div>
                                <label for="category_icon" class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                    Icône représentative
                                </label>
                                <select
                                    id="category_icon"
                                    name="icon_name"
                                    x-model="formData.icon_name"
                                    class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                                >
                                    <option value="laptop">Ordinateur portable (laptop)</option>
                                    <option value="monitor">Écran / Moniteur (monitor)</option>
                                    <option value="cpu">Unité centrale / Tour (cpu)</option>
                                    <option value="server">Serveur (server)</option>
                                    <option value="printer">Imprimante (printer)</option>
                                    <option value="smartphone">Smartphone (smartphone)</option>
                                    <option value="tablet">Tablette (tablet)</option>
                                    <option value="hard-drive">Stockage / Disque dur (hard-drive)</option>
                                    <option value="wifi">Réseau & Wi-Fi (wifi)</option>
                                    <option value="cable">Câblage & Adaptateurs (cable)</option>
                                    <option value="headphones">Accessoires & Audio (headphones)</option>
                                    <option value="wrench">Composants & Pièces (wrench)</option>
                                    <option value="boxes">Consommables (boxes)</option>
                                    <option value="layers">Générique (layers)</option>
                                </select>
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="category_statut" class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                    Statut <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="category_statut"
                                    name="statut"
                                    x-model="formData.statut"
                                    class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                                >
                                    <option value="actif">Active (visible pour nouveaux produits)</option>
                                    <option value="archive">Archivée (masquée)</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="category_description" class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                    Description informative
                                </label>
                                <textarea
                                    id="category_description"
                                    name="description"
                                    x-model="formData.description"
                                    rows="4"
                                    placeholder="Précisez le périmètre d'équipements concernés..."
                                    class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-2xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Pied du drawer -->
                        <div class="p-5 sm:p-6 border-t border-border/60 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                            <button
                                type="button"
                                @click="drawerOpen = false"
                                class="px-5 py-2.5 bg-surface rounded-full text-xs font-semibold text-content hover:bg-slate-100 transition-colors"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                class="px-6 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-bold rounded-full transition-colors"
                                x-text="isEditing ? 'Enregistrer les modifications' : 'Créer la catégorie'"
                            ></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modale de confirmation de suppression -->
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
                        <div class="w-11 h-11 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            {!! $svg('trash-2') !!}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-content">Supprimer la catégorie</h3>
                            <p class="text-xs text-content-secondary mt-1">
                                Êtes-vous sûr de vouloir supprimer définitivement la catégorie
                                <strong class="text-content break-words" x-text="categoryToDelete.nom"></strong> ?
                            </p>
                            <p class="text-[11px] text-content-secondary mt-2">
                                Cette action est irréversible. Seules les catégories sans produit associé peuvent être supprimées.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-border/60 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                        <button
                            type="button"
                            @click="deleteModalOpen = false"
                            class="px-5 py-2.5 bg-surface rounded-full text-xs font-semibold text-content hover:bg-slate-100 transition-colors"
                        >
                            Annuler
                        </button>
                        <form :action="'/it/categories/' + categoryToDelete.id" method="POST" class="flex">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="w-full px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-full transition-colors"
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