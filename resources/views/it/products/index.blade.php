<x-layouts.dashboard title="Gestion du matériel" :breadcrumbs="['Gestion du matériel' => null]">

    <div>
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-emerald-800 text-sm">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between gap-3 text-red-800 text-sm">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="monitor" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Gestion du matériel</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Catalogue des produits informatiques, suivi des quantités disponibles en stock et réapprovisionnements.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center">
                <a
                    href="{{ route('it.categories.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="layers" class="w-4 h-4 text-content-secondary" />
                    Gérer les catégories
                </a>
                <a
                    href="{{ route('it.products.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    Ajouter un matériel
                </a>
            </div>
        </div>

        <!-- Les 4 cartes statistiques (règles de calcul strictement identiques au Dashboard) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Carte 1 — Produits enregistrés -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        Produits enregistrés
                    </p>
                    <p class="text-2xl font-extrabold text-content">
                        {{ $totalProducts }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="package" class="w-6 h-6" />
                </div>
            </div>

            <!-- Carte 2 — Total des unités en stock -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        Total des unités en stock
                    </p>
                    <p class="text-2xl font-extrabold text-primary">
                        {{ number_format($totalUnitesStock) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary-light flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-6 h-6" />
                </div>
            </div>

            <!-- Carte 3 — Stock faible -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        Stock faible
                    </p>
                    <p class="text-2xl font-extrabold text-accent">
                        {{ $stockFaibleCount }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="alert-triangle" class="w-6 h-6" />
                </div>
            </div>

            <!-- Carte 4 — Produits épuisés -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        Produits épuisés
                    </p>
                    <p class="text-2xl font-extrabold {{ $produitsEpuisesCount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $produitsEpuisesCount }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $produitsEpuisesCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="package-x" class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- Barre de filtres simplifiée (3 éléments : Recherche, Catégorie, État du stock + Réinitialiser) -->
        <div class="bg-white rounded-2xl border border-border p-4 mb-6">
            <form method="GET" action="{{ route('it.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- 1. Recherche : Nom, Marque, Référence -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Recherche par nom, marque, référence..."
                        class="block w-full pl-10 pr-4 py-2 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    />
                </div>

                <!-- 2. Catégorie (depuis la base de données) -->
                <div>
                    <select
                        name="category_id"
                        onchange="this.form.submit()"
                        class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. État du stock (Disponible > 1, Stock faible == 1, Rupture == 0) -->
                <div>
                    <select
                        name="etat_stock"
                        onchange="this.form.submit()"
                        class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les stocks</option>
                        <option value="disponible" {{ $etatStock === 'disponible' ? 'selected' : '' }}>Disponible (> 1)</option>
                        <option value="faible" {{ $etatStock === 'faible' ? 'selected' : '' }}>Stock faible (= 1)</option>
                        <option value="rupture" {{ $etatStock === 'rupture' ? 'selected' : '' }}>Rupture de stock (= 0)</option>
                    </select>
                </div>

                <!-- Boutons Filtrer & Réinitialiser -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 py-2 px-4 bg-primary hover:bg-primary-light text-white text-xs font-semibold rounded-xl transition-colors"
                    >
                        <x-ui.icon name="filter" class="w-4 h-4" />
                        Filtrer
                    </button>

                    @if ($search || $categoryId || $etatStock)
                        <a
                            href="{{ route('it.products.index') }}"
                            class="inline-flex items-center justify-center p-2 border border-border bg-white text-content-secondary hover:text-content hover:bg-surface rounded-xl transition-colors flex-shrink-0"
                            title="Réinitialiser les filtres"
                        >
                            <x-ui.icon name="refresh-cw" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tableau simplifié des matériels -->
        <!-- Colonnes : 1. Matériel (nom, marque, modèle) | 2. Catégorie | 3. Quantité disponible | 4. État du stock | 5. Actions -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden">
            @if ($products->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        <x-ui.icon name="package" class="w-8 h-8" />
                    </div>
                    <h3 class="text-base font-bold text-content">Aucun produit trouvé</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">
                        @if ($search || $categoryId || $etatStock)
                            Aucun matériel ne correspond à vos critères de recherche actuels.
                        @else
                            Votre catalogue de matériel informatique est actuellement vide.
                        @endif
                    </p>
                    <div class="mt-5">
                        @if ($search || $categoryId || $etatStock)
                            <a href="{{ route('it.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-content text-xs font-semibold rounded-xl border border-border hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
                            </a>
                        @else
                            <a
                                href="{{ route('it.products.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-light text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                            >
                                <x-ui.icon name="plus" class="w-4 h-4" />
                                Ajouter un matériel
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border bg-surface/50 text-[11px] font-bold text-content-secondary uppercase tracking-wider">
                                <th class="py-3.5 px-6">Matériel</th>
                                <th class="py-3.5 px-6">Catégorie</th>
                                <th class="py-3.5 px-6 text-center">Quantité disponible</th>
                                <th class="py-3.5 px-6 text-center">État du stock</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @foreach ($products as $p)
                                @php
                                    $qteDispo = (int) $p->stocks->sum('quantite');
                                    // Définition de l'état du stock selon la règle stricte :
                                    // > 1 : Disponible (vert)
                                    // == 1 : Stock faible (orange)
                                    // == 0 : Rupture de stock (rouge)
                                    if ($qteDispo === 0) {
                                        $etatLabel = 'Rupture de stock';
                                        $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                                        $dotBg = 'bg-rose-500';
                                    } elseif ($qteDispo === 1) {
                                        $etatLabel = 'Stock faible';
                                        $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                                        $dotBg = 'bg-amber-500';
                                    } else {
                                        $etatLabel = 'Disponible';
                                        $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        $dotBg = 'bg-emerald-500';
                                    }
                                @endphp
                                <tr class="hover:bg-surface/40 transition-colors">
                                    <!-- 1. Matériel : nom du produit, marque et modèle -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            @if ($p->photo_url)
                                                <img
                                                    src="{{ $p->photo_url }}"
                                                    alt="{{ $p->nom }}"
                                                    class="w-10 h-10 rounded-xl object-cover border border-border flex-shrink-0"
                                                />
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-surface border border-border flex items-center justify-center text-content-secondary flex-shrink-0">
                                                    <x-ui.icon :name="$p->categorie->icon_name ?? 'package'" class="w-5 h-5" />
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('it.products.show', $p) }}" class="font-bold text-content hover:text-primary transition-colors block text-sm">
                                                    {{ $p->nom }}
                                                </a>
                                                <div class="flex items-center gap-1.5 text-[11px] text-content-secondary mt-0.5">
                                                    @if ($p->marque)
                                                        <span class="font-medium text-content">{{ $p->marque }}</span>
                                                    @endif
                                                    @if ($p->marque && $p->modele)
                                                        <span>&bull;</span>
                                                    @endif
                                                    @if ($p->modele)
                                                        <span>{{ $p->modele }}</span>
                                                    @endif
                                                    @if ($p->reference)
                                                        <span class="font-mono text-content-secondary/70">({{ $p->reference }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Catégorie -->
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface text-[11px] font-medium text-content border border-border">
                                            <x-ui.icon :name="$p->categorie->icon_name ?? 'layers'" class="w-3.5 h-3.5 text-content-secondary" />
                                            {{ $p->categorie->nom ?? 'Non classé' }}
                                        </span>
                                    </td>

                                    <!-- 3. Quantité disponible -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="text-sm font-extrabold text-content">
                                            {{ $qteDispo }}
                                        </span>
                                        <span class="text-[11px] text-content-secondary block">
                                            {{ $qteDispo > 1 ? 'unités' : 'unité' }}
                                        </span>
                                    </td>

                                    <!-- 4. État du stock (Disponible: vert, Faible: orange, Rupture: rouge) -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border {{ $badgeBg }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotBg }}"></span>
                                            {{ $etatLabel }}
                                        </span>
                                    </td>

                                    <!-- 5. Actions : Voir, Modifier, Archiver (avec confirmation) -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Voir -->
                                            <a
                                                href="{{ route('it.products.show', $p) }}"
                                                class="p-2 rounded-lg text-content-secondary hover:text-primary hover:bg-primary/5 transition-colors"
                                                title="Consulter le matériel"
                                            >
                                                <x-ui.icon name="eye" class="w-4 h-4" />
                                            </a>

                                            <!-- Modifier -->
                                            <a
                                                href="{{ route('it.products.edit', $p) }}"
                                                class="p-2 rounded-lg text-content-secondary hover:text-primary hover:bg-primary/5 transition-colors"
                                                title="Modifier les informations"
                                            >
                                                <x-ui.icon name="pencil" class="w-4 h-4" />
                                            </a>

                                            <!-- Archiver avec confirmation -->
                                            <form
                                                method="POST"
                                                action="{{ route('it.products.toggle-status', $p) }}"
                                                class="inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir archiver le matériel « {{ addslashes($p->nom) }} » ?');"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="p-2 rounded-lg text-content-secondary hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                                    title="Archiver"
                                                >
                                                    <x-ui.icon name="archive" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-border">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-layouts.dashboard>
