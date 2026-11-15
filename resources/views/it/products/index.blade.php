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
                        Ajoutez et modifiez vos produits.
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

        <!-- 4 Cartes Statistiques par Situation -->
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
                    <p class="text-[11px] text-content-secondary mt-0.5">Références actives</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="package" class="w-6 h-6" />
                </div>
            </div>

            <!-- Carte 2 — En stock -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        En stock
                    </p>
                    <p class="text-2xl font-extrabold text-emerald-600">
                        {{ number_format($totalEnStock) }}
                    </p>
                    <p class="text-[11px] text-content-secondary mt-0.5">Disponibles en réserve</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-6 h-6" />
                </div>
            </div>

            <!-- Carte 3 — En utilisation -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        En utilisation
                    </p>
                    <p class="text-2xl font-extrabold text-primary">
                        {{ number_format($totalEnUtilisation) }}
                    </p>
                    <p class="text-[11px] text-content-secondary mt-0.5">Bureaux et production</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="laptop" class="w-6 h-6" />
                </div>
            </div>

            <!-- Carte 4 — En panne -->
            <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                        En panne
                    </p>
                    <p class="text-2xl font-extrabold {{ $totalEnPanne > 0 ? 'text-rose-600' : 'text-content' }}">
                        {{ number_format($totalEnPanne) }}
                    </p>
                    <p class="text-[11px] text-content-secondary mt-0.5">Hors service / à réparer</p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $totalEnPanne > 0 ? 'bg-rose-50 text-rose-600' : 'bg-surface text-content-secondary' }} flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="alert-octagon" class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- Barre de filtres par situation -->
        <div class="bg-white rounded-2xl border border-border p-4 mb-6 shadow-xs">
            <form method="GET" action="{{ route('it.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- 1. Recherche -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Recherche par nom, référence..."
                        class="block w-full pl-10 pr-4 py-2 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    />
                </div>

                <!-- 2. Catégorie -->
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

                <!-- 3. Situation : Tous, En stock, En utilisation, En panne -->
                <div>
                    <select
                        name="situation"
                        onchange="this.form.submit()"
                        class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Toutes les situations</option>
                        <option value="en_stock" {{ $situation === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ $situation === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ $situation === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- 4. Niveau du stock -->
                <div>
                    <select
                        name="etat_stock"
                        onchange="this.form.submit()"
                        class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les niveaux</option>
                        <option value="disponible" {{ $etatStock === 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="faible" {{ $etatStock === 'faible' ? 'selected' : '' }}>Stock faible</option>
                        <option value="rupture" {{ $etatStock === 'rupture' ? 'selected' : '' }}>Épuisé</option>
                    </select>
                </div>

                <!-- Boutons Filtrer & Réinitialiser -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 py-2 px-4 bg-primary hover:bg-primary-light text-white text-xs font-semibold rounded-xl transition-colors cursor-pointer"
                    >
                        <x-ui.icon name="filter" class="w-4 h-4" />
                        Filtrer
                    </button>

                    @if ($search || $categoryId || $situation || $etatStock)
                        <a
                            href="{{ route('it.products.index') }}"
                            class="inline-flex items-center justify-center p-2 border border-border bg-white text-content-secondary hover:text-content hover:bg-surface rounded-xl transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            <x-ui.icon name="refresh-cw" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tableau des matériels avec colonnes par situation -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden">
            @if ($products->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        <x-ui.icon name="package" class="w-8 h-8" />
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
                                <th class="py-3.5 px-4 text-center">En stock</th>
                                <th class="py-3.5 px-4 text-center">En utilisation</th>
                                <th class="py-3.5 px-4 text-center">En panne</th>
                                <th class="py-3.5 px-4 text-center">Total</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @foreach ($products as $p)
                                @php
                                    $enStock = $p->quantite_en_stock;
                                    $enUtil = $p->quantite_en_utilisation;
                                    $enPanne = $p->quantite_en_panne;
                                    $total = $p->total_stock;
                                    $status = $p->stock_status; // rupture, faible, normal
                                @endphp
                                <tr class="hover:bg-surface/40 transition-colors">
                                    <!-- Matériel -->
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-surface border border-border/80 flex items-center justify-center flex-shrink-0 text-content-secondary">
                                                @if ($p->photo_url)
                                                    <img src="{{ $p->photo_url }}" alt="{{ $p->nom }}" class="w-full h-full object-cover rounded-lg" onerror="this.style.display='none'; this.parentElement.innerHTML='<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' class=\'w-4 h-4\'><rect x=\'2\' y=\'3\' width=\'20\' height=\'14\' rx=\'2\' ry=\'2\'/><line x1=\'8\' y1=\'21\' x2=\'16\' y2=\'21\'/><line x1=\'12\' y1=\'17\' x2=\'12\' y2=\'21\'/></svg>';" />
                                                @else
                                                    <x-ui.icon name="monitor" class="w-4 h-4" />
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('it.products.show', $p) }}" class="font-bold text-content hover:text-primary transition-colors truncate block">
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

                                    <!-- Catégorie -->
                                    <td class="py-3.5 px-6 text-content">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-surface text-content border border-border/60">
                                            {{ $p->categorie->nom ?? 'Non classé' }}
                                        </span>
                                    </td>

                                    <!-- En stock (réserve disponible) -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg font-bold text-xs {{ $enStock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-surface text-content-secondary border border-border/60' }}">
                                            {{ $enStock }}
                                        </span>
                                    </td>

                                    <!-- En utilisation -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg font-bold text-xs {{ $enUtil > 0 ? 'bg-blue-50 text-primary border border-blue-200' : 'bg-surface text-content-secondary border border-border/60' }}">
                                            {{ $enUtil }}
                                        </span>
                                    </td>

                                    <!-- En panne -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg font-bold text-xs {{ $enPanne > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-surface text-content-secondary border border-border/60' }}">
                                            {{ $enPanne }}
                                        </span>
                                    </td>

                                    <!-- Total -->
                                    <td class="py-3.5 px-4 text-center font-extrabold text-content text-sm">
                                        {{ $total }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-6 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <a
                                                href="{{ route('it.products.show', $p) }}"
                                                class="p-1.5 text-content-secondary hover:text-primary hover:bg-surface rounded-lg transition-colors"
                                                title="Consulter la fiche"
                                            >
                                                <x-ui.icon name="eye" class="w-4 h-4" />
                                            </a>
                                            <a
                                                href="{{ route('it.products.edit', $p) }}"
                                                class="p-1.5 text-content-secondary hover:text-primary hover:bg-surface rounded-lg transition-colors"
                                                title="Modifier"
                                            >
                                                <x-ui.icon name="edit-2" class="w-4 h-4" />
                                            </a>
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
