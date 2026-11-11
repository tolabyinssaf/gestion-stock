<x-layouts.dashboard title="Gestion du stock" :breadcrumbs="['Gestion du stock' => null]">

    <div
        x-data="{
            // Modale Entrée (+)
            entreeModalOpen: false,
            entreeProduct: { id: null, nom: '', ref: '', totalStock: 0, stocks: [] },
            entreeEmplacementId: '',
            entreeQuantite: 1,
            get entreeStockActuel() {
                if (!this.entreeEmplacementId || !this.entreeProduct || !this.entreeProduct.stocks) {
                    return this.entreeProduct?.totalStock || 0;
                }
                const st = this.entreeProduct.stocks.find(s => s.emplacement_id == this.entreeEmplacementId);
                return st ? parseInt(st.quantite) || 0 : 0;
            },
            get entreeNouveauStock() {
                const q = parseInt(this.entreeQuantite) || 0;
                return this.entreeStockActuel + Math.max(0, q);
            },
            openEntreeModal(prod) {
                this.entreeProduct = prod;
                this.entreeQuantite = 1;
                this.entreeEmplacementId = prod.stocks && prod.stocks.length > 0 ? prod.stocks[0].emplacement_id : '{{ $emplacements->first()?->id ?? "" }}';
                this.entreeModalOpen = true;
            },

            // Modale Sortie (-)
            sortieModalOpen: false,
            sortieProduct: { id: null, nom: '', ref: '', totalStock: 0, stocks: [] },
            sortieEmplacementId: '',
            sortieQuantite: 1,
            get sortieStockActuel() {
                if (!this.sortieProduct || !this.sortieProduct.stocks) return 0;
                const st = this.sortieProduct.stocks.find(s => s.emplacement_id == this.sortieEmplacementId);
                return st ? parseInt(st.quantite) || 0 : 0;
            },
            get sortieNouveauStock() {
                const q = parseInt(this.sortieQuantite) || 0;
                return Math.max(0, this.sortieStockActuel - q);
            },
            get sortieValide() {
                const q = parseInt(this.sortieQuantite);
                return q > 0 && q <= this.sortieStockActuel;
            },
            openSortieModal(prod) {
                this.sortieProduct = prod;
                this.sortieQuantite = 1;
                const stWithStock = prod.stocks?.find(s => s.quantite > 0);
                this.sortieEmplacementId = stWithStock ? stWithStock.emplacement_id : (prod.stocks && prod.stocks.length > 0 ? prod.stocks[0].emplacement_id : '');
                this.sortieModalOpen = true;
            },

            // Alertes repliables
            showAllAlerts: false
        }"
        @keydown.escape.window="entreeModalOpen = false; sortieModalOpen = false"
    >
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-emerald-800 text-sm shadow-xs">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between gap-3 text-rose-800 text-sm shadow-xs">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0" />
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Gestion du stock</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Ajustez les quantités en temps réel grâce aux boutons <strong>+</strong> et <strong>−</strong>, suivez les emplacements et prévenez les ruptures.
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <a
                    href="{{ route('it.emplacements.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="map-pin" class="w-4 h-4 text-content-secondary" />
                    Gérer les emplacements
                </a>
                <a
                    href="{{ route('it.stocks.transfert') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="arrow-right-left" class="w-4 h-4 text-content-secondary" />
                    Transfert
                </a>
                <a
                    href="{{ route('it.stocks.correction') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-accent hover:bg-accent/5 transition-colors"
                >
                    <x-ui.icon name="wrench" class="w-4 h-4 text-accent" />
                    Correction
                </a>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- 1. ALERTES DE STOCK EN HAUT DE PAGE                       -->
        <!-- Masquée entièrement si aucun produit concerné            -->
        <!-- ========================================================= -->
        @if (!empty($alertesStock) && count($alertesStock) > 0)
            <div class="mb-8 bg-white rounded-2xl border border-border p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <x-ui.icon name="alert-triangle" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                                Alertes de stock prioritaires
                            </h3>
                            <p class="text-xs text-content-secondary">
                                Ces articles nécessitent une commande de réapprovisionnement immédiate
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                            {{ count($alertesStock) }} produit{{ count($alertesStock) > 1 ? 's' : '' }} critique{{ count($alertesStock) > 1 ? 's' : '' }}
                        </span>
                        @if (count($alertesStock) > 3)
                            <button
                                type="button"
                                @click="showAllAlerts = !showAllAlerts"
                                class="text-xs font-semibold text-primary hover:underline flex items-center gap-1"
                            >
                                <span x-text="showAllAlerts ? 'Réduire' : 'Voir les ' + {{ count($alertesStock) }} + ' alertes'"></span>
                                <x-ui.icon name="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" ::class="showAllAlerts ? 'rotate-180' : ''" />
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Grille des alertes -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($alertesStock as $index => $alerte)
                        <div
                            class="p-3.5 rounded-xl border flex items-center justify-between gap-3 transition-colors {{ $alerte['type'] === 'rupture' ? 'bg-rose-50/50 border-rose-200 hover:bg-rose-50' : 'bg-amber-50/50 border-amber-200 hover:bg-amber-50' }}"
                            @if ($index >= 3) x-show="showAllAlerts" x-cloak @endif
                        >
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if ($alerte['type'] === 'rupture')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            Rupture de stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            Stock faible
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-xs font-bold text-content truncate" title="{{ $alerte['nom'] }}">
                                    {{ $alerte['nom'] }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-content-secondary">
                                    <span>Réf: {{ $alerte['produit']->reference }}</span>
                                    <span>&bull;</span>
                                    <span class="font-semibold {{ $alerte['type'] === 'rupture' ? 'text-rose-700' : 'text-amber-800' }}">
                                        {{ $alerte['quantite'] }} unité restante
                                    </span>
                                </div>
                            </div>

                            <!-- Bouton Demander -->
                            <a
                                href="{{ $alerte['gmail_url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold text-white shadow-2xs transition-colors flex-shrink-0 {{ $alerte['type'] === 'rupture' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-amber-600 hover:bg-amber-700' }}"
                                title="Ouvrir Gmail avec le message prérempli"
                            >
                                <x-ui.icon name="mail" class="w-3.5 h-3.5" />
                                <span>Demander</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- ========================================================= -->
        <!-- 2. CARTES STATISTIQUES (4 indicateurs harmonisés)          -->
        <!-- ========================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- 1. Total unités en stock -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">Total unités en stock</span>
                    <span class="text-xl font-extrabold text-primary mt-0.5 block">{{ number_format($kpis['total_unites_stock']) }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-primary-light flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-5 h-5" />
                </div>
            </div>

            <!-- 2. Articles en stock normal -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">Stock suffisant (&gt; 1)</span>
                    <span class="text-xl font-extrabold text-emerald-700 mt-0.5 block">{{ $kpis['articles_en_stock'] }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="check-circle" class="w-5 h-5" />
                </div>
            </div>

            <!-- 3. Stock faible (exactement 1) -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">Stock faible (= 1)</span>
                    <span class="text-xl font-extrabold text-accent mt-0.5 block">{{ $kpis['stock_faible'] }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5" />
                </div>
            </div>

            <!-- 4. Produits épuisés (exactement 0) -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">Produits épuisés (= 0)</span>
                    <span class="text-xl font-extrabold {{ $kpis['rupture_stock'] > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-0.5 block">{{ $kpis['rupture_stock'] }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl {{ $kpis['rupture_stock'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="package-x" class="w-5 h-5" />
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- 3. BARRE DE FILTRES ET RECHERCHE                          -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('it.stocks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <!-- Search input -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher désignation, référence..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- Category filter -->
                <div>
                    <select
                        name="category_id"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Emplacement filter -->
                <div>
                    <select
                        name="emplacement_id"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous emplacements</option>
                        @foreach ($emplacements as $emp)
                            <option value="{{ $emp->id }}" {{ request('emplacement_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock alert filter -->
                <div class="flex items-center gap-2">
                    <select
                        name="stock_alert"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les états</option>
                        <option value="rupture" {{ request('stock_alert') === 'rupture' ? 'selected' : '' }}>Rupture (= 0)</option>
                        <option value="faible" {{ request('stock_alert') === 'faible' ? 'selected' : '' }}>Stock faible (= 1)</option>
                        <option value="normal" {{ request('stock_alert') === 'normal' ? 'selected' : '' }}>Stock suffisant (&gt; 1)</option>
                    </select>

                    <button
                        type="submit"
                        class="px-4 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-semibold transition-colors flex-shrink-0"
                    >
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'category_id', 'emplacement_id', 'stock_alert']))
                        <a
                            href="{{ route('it.stocks.index') }}"
                            class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ========================================================= -->
        <!-- 4. TABLEAU SIMPLIFIÉ : LISTE DES STOCKS                   -->
        <!-- ========================================================= -->
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                        <x-ui.icon name="boxes" class="w-4 h-4 text-primary" />
                        Liste des stocks
                    </h3>
                    <p class="text-xs text-content-secondary mt-0.5">Gestion des quantités par produit et répartition par emplacement physique</p>
                </div>
                <span class="text-xs font-semibold text-content-secondary bg-surface px-2.5 py-1 rounded-lg border border-border">
                    {{ $produits->total() }} références
                </span>
            </div>

            <x-ui.table
                :headers="['Produit', 'Catégorie', 'Répartition par emplacement', 'Stock total', 'État du stock', 'Actions']"
                :isEmpty="$produits->isEmpty()"
                emptyIcon="boxes"
                emptyTitle="Aucun produit trouvé"
                emptyDescription="Aucun article géré en stock ne correspond à vos critères."
            >
                @foreach ($produits as $produit)
                    @php
                        $nomProd = $produit->nom ?? $produit->designation;
                        $totalStock = (int) $produit->stocks->sum('quantite');
                    @endphp
                    <tr class="hover:bg-surface/50 transition-colors">
                        <!-- Produit -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <x-ui.icon name="package" class="w-4 h-4" />
                                </div>
                                <div>
                                    <a href="{{ route('it.products.show', $produit) }}" class="font-semibold text-content hover:text-primary transition-colors text-xs">
                                        {{ $nomProd }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-content-secondary">
                                        <span>Réf: {{ $produit->reference }}</span>
                                        @if($produit->marque)
                                            <span>&bull; {{ $produit->marque }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Catégorie -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-surface text-content-secondary border border-border">
                                {{ $produit->categorie->nom ?? 'Non classé' }}
                            </span>
                        </td>

                        <!-- Répartition par emplacement -->
                        <td class="px-5 py-4">
                            @if($produit->stocks->isEmpty() || $produit->stocks->sum('quantite') == 0)
                                <span class="text-xs text-content-secondary italic">Aucun stock consigné</span>
                            @else
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @foreach($produit->stocks as $stockLine)
                                        @if($stockLine->quantite > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50/60 border border-blue-200/60 text-primary">
                                                <span>{{ $stockLine->emplacement->nom ?? 'Inconnu' }} :</span>
                                                <strong class="font-bold">{{ $stockLine->quantite }}</strong>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        <!-- Stock total -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold {{ $totalStock === 0 ? 'text-rose-600' : ($totalStock === 1 ? 'text-accent' : 'text-content') }}">
                                {{ $totalStock }} <span class="text-xs font-normal text-content-secondary">unités</span>
                            </span>
                        </td>

                        <!-- État du stock (Règles strictes : 0 = Rupture, 1 = Faible, > 1 = Suffisant) -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if ($totalStock === 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Rupture de stock
                                </span>
                            @elseif ($totalStock === 1)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Stock faible (1)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Suffisant
                                </span>
                            @endif
                        </td>

                        <!-- Actions (+ et − clearly visible with Lucide icons) -->
                        <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Bouton Vert « + » : Entrée de stock -->
                                <button
                                    type="button"
                                    @click="openEntreeModal({
                                        id: {{ $produit->id }},
                                        nom: '{{ addslashes($nomProd) }}',
                                        ref: '{{ $produit->reference }}',
                                        totalStock: {{ $totalStock }},
                                        stocks: {{ json_encode($produit->stocks->map(fn($s) => ['emplacement_id' => $s->emplacement_id, 'nom' => $s->emplacement->nom ?? 'Inconnu', 'quantite' => (int) $s->quantite])) }}
                                    })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs transition-transform active:scale-95"
                                    title="Entrée de stock (+)"
                                >
                                    <x-ui.icon name="plus" class="w-4 h-4" />
                                </button>

                                <!-- Bouton Orange « − » : Sortie de stock -->
                                <button
                                    type="button"
                                    @click="openSortieModal({
                                        id: {{ $produit->id }},
                                        nom: '{{ addslashes($nomProd) }}',
                                        ref: '{{ $produit->reference }}',
                                        totalStock: {{ $totalStock }},
                                        stocks: {{ json_encode($produit->stocks->map(fn($s) => ['emplacement_id' => $s->emplacement_id, 'nom' => $s->emplacement->nom ?? 'Inconnu', 'quantite' => (int) $s->quantite])) }}
                                    })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#FE7A04] hover:bg-[#e06c04] text-white shadow-2xs transition-transform active:scale-95 disabled:opacity-35 disabled:cursor-not-allowed"
                                    {{ $totalStock <= 0 ? 'disabled' : '' }}
                                    title="Sortie de stock (−)"
                                >
                                    <x-ui.icon name="minus" class="w-4 h-4" />
                                </button>

                                <!-- Action secondaire : Transférer -->
                                <a
                                    href="{{ route('it.stocks.transfert', ['produit_id' => $produit->id]) }}"
                                    class="p-1.5 text-content-secondary hover:text-primary hover:bg-surface rounded-lg transition-colors"
                                    title="Transférer vers un autre emplacement"
                                >
                                    <x-ui.icon name="arrow-right-left" class="w-4 h-4" />
                                </a>

                                <!-- Action secondaire : Demande de réapprovisionnement -->
                                <a
                                    href="{{ route('it.demandes.create', ['produit_id' => $produit->id]) }}"
                                    class="p-1.5 text-content-secondary hover:text-accent hover:bg-accent/10 rounded-lg transition-colors"
                                    title="Demander du réapprovisionnement"
                                >
                                    <x-ui.icon name="mail" class="w-4 h-4" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach

                <x-slot:footer>
                    <div class="w-full">
                        {{ $produits->links() }}
                    </div>
                </x-slot:footer>
            </x-ui.table>
        </div>

        <!-- ========================================================= -->
        <!-- MODALE DE CONFIRMATION 1 : ENTRÉE DE STOCK (+)           -->
        <!-- ========================================================= -->
        <div
            x-show="entreeModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-entree-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div
                    x-show="entreeModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity"
                    @click="entreeModalOpen = false"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div
                    x-show="entreeModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-border"
                >
                    <form action="{{ route('it.stocks.entree.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produit_id" :value="entreeProduct.id">
                        <input type="hidden" name="date_mouvement" value="{{ now()->toDateString() }}">

                        <div class="p-6">
                            <!-- En-tête de la modale -->
                            <div class="flex items-center justify-between pb-4 border-b border-border">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <x-ui.icon name="plus" class="w-5 h-5 font-bold" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-content" id="modal-entree-title">
                                            Entrée de stock
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Opération : Réception
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="entreeModalOpen = false" class="text-content-secondary hover:text-content p-1 rounded-lg">
                                    <x-ui.icon name="x" class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Produit concerné -->
                            <div class="my-4 p-3 bg-surface rounded-xl border border-border">
                                <p class="text-xs font-bold text-content truncate" x-text="entreeProduct.nom"></p>
                                <p class="text-[11px] text-content-secondary mt-0.5">
                                    Réf: <span class="font-mono text-content" x-text="entreeProduct.ref"></span>
                                </p>
                            </div>

                            <!-- Champs opérationnels -->
                            <div class="space-y-4">
                                <!-- Emplacement de réception -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Emplacement concerné <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        name="emplacement_id"
                                        x-model="entreeEmplacementId"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                                    >
                                        @foreach ($emplacements as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ $emp->nom }} ({{ $emp->code }}) @if($emp->site) - {{ $emp->site }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantité -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Quantité à ajouter <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="quantite"
                                        min="1"
                                        step="1"
                                        x-model.number="entreeQuantite"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-sm font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                                    >
                                    <p class="text-[11px] text-content-secondary mt-1">Un entier strictement positif est requis.</p>
                                </div>

                                <!-- Calcul automatique du nouveau stock -->
                                <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl flex items-center justify-between text-xs">
                                    <span class="text-content-secondary">
                                        Stock actuel à l'emplacement : <strong class="text-content" x-text="entreeStockActuel"></strong>
                                    </span>
                                    <span class="font-bold text-emerald-700">
                                        Nouveau stock : <span class="text-sm font-extrabold" x-text="entreeNouveauStock"></span> unités
                                    </span>
                                </div>

                                <!-- Motif -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Motif de l'entrée <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="motif"
                                        value="Réapprovisionnement de stock"
                                        required
                                        placeholder="ex: Réapprovisionnement stock, achat..."
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all"
                                    >
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="pt-5 mt-5 border-t border-border flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    @click="entreeModalOpen = false"
                                    class="px-4 py-2.5 border border-border hover:bg-surface text-content-secondary hover:text-content rounded-xl text-xs font-semibold transition-colors"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    :disabled="!entreeQuantite || entreeQuantite < 1"
                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1.5"
                                >
                                    <x-ui.icon name="check" class="w-4 h-4" />
                                    <span>Confirmer</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODALE DE CONFIRMATION 2 : SORTIE DE STOCK (−)            -->
        <!-- ========================================================= -->
        <div
            x-show="sortieModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-sortie-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div
                    x-show="sortieModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity"
                    @click="sortieModalOpen = false"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div
                    x-show="sortieModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-border"
                >
                    <form action="{{ route('it.stocks.sortie.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produit_id" :value="sortieProduct.id">
                        <input type="hidden" name="date_mouvement" value="{{ now()->toDateString() }}">

                        <div class="p-6">
                            <!-- En-tête de la modale -->
                            <div class="flex items-center justify-between pb-4 border-b border-border">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-accent flex items-center justify-center">
                                        <x-ui.icon name="minus" class="w-5 h-5 font-bold" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-content" id="modal-sortie-title">
                                            Sortie de stock
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-accent border border-orange-200">
                                            Opération : Déstockage
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="sortieModalOpen = false" class="text-content-secondary hover:text-content p-1 rounded-lg">
                                    <x-ui.icon name="x" class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Produit concerné -->
                            <div class="my-4 p-3 bg-surface rounded-xl border border-border">
                                <p class="text-xs font-bold text-content truncate" x-text="sortieProduct.nom"></p>
                                <p class="text-[11px] text-content-secondary mt-0.5">
                                    Réf: <span class="font-mono text-content" x-text="sortieProduct.ref"></span>
                                </p>
                            </div>

                            <!-- Champs opérationnels -->
                            <div class="space-y-4">
                                <!-- Emplacement source -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Emplacement source <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        name="emplacement_id"
                                        x-model="sortieEmplacementId"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                                    >
                                        <template x-if="!sortieProduct.stocks || sortieProduct.stocks.length === 0">
                                            <option value="" disabled selected>Aucun stock consigné</option>
                                        </template>
                                        <template x-for="st in sortieProduct.stocks" :key="st.emplacement_id">
                                            <option :value="st.emplacement_id" x-text="st.nom + ' (' + st.quantite + ' dispo)'"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantité -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-bold text-content uppercase tracking-wider">
                                            Quantité à retirer <span class="text-rose-500">*</span>
                                        </label>
                                        <span class="text-xs text-content-secondary">
                                            Disponible : <strong class="text-accent" x-text="sortieStockActuel"></strong>
                                        </span>
                                    </div>
                                    <input
                                        type="number"
                                        name="quantite"
                                        min="1"
                                        :max="sortieStockActuel"
                                        step="1"
                                        x-model.number="sortieQuantite"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-sm font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                                    >
                                    <p x-show="sortieQuantite > sortieStockActuel" class="text-[11px] text-rose-600 font-semibold mt-1">
                                        Impossible de retirer plus que le stock disponible sur cet emplacement.
                                    </p>
                                </div>

                                <!-- Calcul automatique du nouveau stock -->
                                <div class="p-3 bg-orange-50/60 border border-orange-200 rounded-xl flex items-center justify-between text-xs">
                                    <span class="text-content-secondary">
                                        Stock actuel : <strong class="text-content" x-text="sortieStockActuel"></strong>
                                    </span>
                                    <span class="font-bold text-accent">
                                        Nouveau stock : <span class="text-sm font-extrabold" x-text="sortieNouveauStock"></span> unités
                                    </span>
                                </div>

                                <!-- Motif -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Motif de la sortie <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="motif"
                                        value="Sortie de stock"
                                        required
                                        placeholder="ex: Attribution, renouvellement..."
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                                    >
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="pt-5 mt-5 border-t border-border flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    @click="sortieModalOpen = false"
                                    class="px-4 py-2.5 border border-border hover:bg-surface text-content-secondary hover:text-content rounded-xl text-xs font-semibold transition-colors"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    :disabled="!sortieValide"
                                    class="px-5 py-2.5 bg-[#FE7A04] hover:bg-[#e06c04] disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1.5"
                                >
                                    <x-ui.icon name="check" class="w-4 h-4" />
                                    <span>Confirmer</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-layouts.dashboard>
