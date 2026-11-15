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
            situationMotif: '',
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
                this.situationMotif = (depart === 'en_stock' && arrivee === 'en_utilisation') ? 'Mise à disposition pour utilisation'
                    : (depart === 'en_utilisation' && arrivee === 'en_stock') ? 'Retour en réserve'
                    : (arrivee === 'en_panne') ? 'Déclaration de panne matérielle'
                    : (depart === 'en_panne') ? 'Remise en service après réparation' : '';
                this.situationConfirmeFonctionnel = false;
                this.situationModalOpen = true;
            },

            // Alertes repliables
            showAllAlerts: false
        }"
        @keydown.escape.window="entreeModalOpen = false; sortieModalOpen = false; situationModalOpen = false"
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
                        Consultez et mettez à jour les quantités de matériel par situation.
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <button
                    type="button"
                    @click="openSituationModal({
                        id: {{ $tousProduits->first()?->id ?? 'null' }},
                        nom: '{{ addslashes($tousProduits->first()?->nom ?? 'Matériel') }}',
                        ref: '{{ $tousProduits->first()?->reference ?? '' }}',
                        en_stock: {{ $produits->first()?->quantite_en_stock ?? 0 }},
                        en_utilisation: {{ $produits->first()?->quantite_en_utilisation ?? 0 }},
                        en_panne: {{ $produits->first()?->quantite_en_panne ?? 0 }},
                        total: {{ $produits->first()?->total_stock ?? 0 }}
                    })"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer"
                >
                    <x-ui.icon name="refresh-cw" class="w-4 h-4" />
                    Changer la situation
                </button>
                <a
                    href="{{ route('it.stocks.correction') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-accent hover:bg-accent/5 transition-colors"
                >
                    <x-ui.icon name="wrench" class="w-4 h-4 text-accent" />
                    Correction
                </a>
            </div>
        </div>

        <!-- Section des Alertes de Stock (basées strictement sur En stock vs seuil) -->
        @if (!empty($alertesStock) && count($alertesStock) > 0)
            <div class="mb-8 bg-white rounded-2xl border border-border p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <x-ui.icon name="alert-triangle" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                                Alertes de stock
                            </h3>
                            <p class="text-xs text-content-secondary">
                                Produits dont le stock disponible en réserve est insuffisant.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                            {{ count($alertesStock) }} produit{{ count($alertesStock) > 1 ? 's' : '' }} en alerte
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($alertesStock as $index => $alerte)
                        <div class="p-3.5 rounded-xl border flex items-center justify-between gap-3 transition-colors {{ $alerte['type'] === 'rupture' ? 'bg-rose-50/50 border-rose-200 hover:bg-rose-50' : 'bg-amber-50/50 border-amber-200 hover:bg-amber-50' }}">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if ($alerte['type'] === 'rupture')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            Épuisé (0 en réserve)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            Stock faible (≤ {{ $alerte['seuil'] }})
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
                                        {{ $alerte['quantite'] }} unité(s) en réserve
                                    </span>
                                </div>
                            </div>

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

        <!-- 4 CARTES STATISTIQUES PAR SITUATION DU MATÉRIEL -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- 1. En stock -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">En stock</span>
                    <span class="text-xl font-extrabold text-emerald-600 mt-0.5 block">{{ number_format($kpis['total_en_stock']) }}</span>
                    <span class="text-[11px] text-content-secondary">Disponible en réserve</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-5 h-5" />
                </div>
            </div>

            <!-- 2. En utilisation -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">En utilisation</span>
                    <span class="text-xl font-extrabold text-primary mt-0.5 block">{{ number_format($kpis['total_en_utilisation']) }}</span>
                    <span class="text-[11px] text-content-secondary">Postes et production</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="laptop" class="w-5 h-5" />
                </div>
            </div>

            <!-- 3. En panne -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">En panne</span>
                    <span class="text-xl font-extrabold {{ $kpis['total_en_panne'] > 0 ? 'text-rose-600' : 'text-content' }} mt-0.5 block">{{ number_format($kpis['total_en_panne']) }}</span>
                    <span class="text-[11px] text-content-secondary">Hors service / à réparer</span>
                </div>
                <div class="w-11 h-11 rounded-xl {{ $kpis['total_en_panne'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-surface text-content-secondary' }} flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="alert-octagon" class="w-5 h-5" />
                </div>
            </div>

            <!-- 4. Total global -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center justify-between gap-3.5 shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block">Total global</span>
                    <span class="text-xl font-extrabold text-content mt-0.5 block">{{ number_format($kpis['total_global']) }}</span>
                    <span class="text-[11px] text-content-secondary">Toutes situations réunies</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-surface text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="pie-chart" class="w-5 h-5" />
                </div>
            </div>
        </div>

        <!-- BARRE DE FILTRES -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('it.stocks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <!-- Recherche -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Rechercher nom, référence..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- Catégorie -->
                <div>
                    <select
                        name="category_id"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Situation : Tous, En stock, En utilisation, En panne -->
                <div>
                    <select
                        name="situation"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Toutes les situations</option>
                        <option value="en_stock" {{ $situation === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ $situation === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ $situation === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- Niveau du stock & Actions -->
                <div class="flex items-center gap-2">
                    <select
                        name="stock_alert"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les états</option>
                        <option value="disponible" {{ $stockAlert === 'disponible' || $stockAlert === 'normal' ? 'selected' : '' }}>Disponible</option>
                        <option value="faible" {{ $stockAlert === 'faible' ? 'selected' : '' }}>Stock faible</option>
                        <option value="rupture" {{ $stockAlert === 'rupture' ? 'selected' : '' }}>Épuisé</option>
                    </select>

                    @if ($search || $categoryId || $situation || $stockAlert)
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

        <!-- TABLEAU ADAPTÉ DU STOCK -->
        <div class="mb-10 bg-white rounded-2xl border border-border shadow-xs overflow-hidden">
            @if ($produits->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        <x-ui.icon name="boxes" class="w-8 h-8" />
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
                            <a href="{{ route('it.stocks.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-content text-xs font-semibold rounded-xl border border-border hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
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
                                <th class="py-3.5 px-4 text-center">En stock</th>
                                <th class="py-3.5 px-4 text-center">En utilisation</th>
                                <th class="py-3.5 px-4 text-center">En panne</th>
                                <th class="py-3.5 px-4 text-center">Total</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @foreach ($produits as $produit)
                                @php
                                    $nomProd = $produit->nom ?? $produit->designation;
                                    $enStock = $produit->quantite_en_stock;
                                    $enUtil = $produit->quantite_en_utilisation;
                                    $enPanne = $produit->quantite_en_panne;
                                    $total = $produit->total_stock;
                                @endphp
                                <tr class="hover:bg-surface/40 transition-colors">
                                    <!-- Matériel -->
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-surface border border-border/80 flex items-center justify-center flex-shrink-0 text-content-secondary">
                                                @if ($produit->photo_url)
                                                    <img src="{{ $produit->photo_url }}" alt="{{ $nomProd }}" class="w-full h-full object-cover rounded-lg" />
                                                @else
                                                    <x-ui.icon name="monitor" class="w-4 h-4" />
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('it.products.show', $produit) }}" class="font-bold text-content hover:text-primary transition-colors truncate block">
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
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-lg font-bold text-xs {{ $enStock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-surface text-content-secondary border border-border/60' }}">
                                            {{ $enStock }}
                                        </span>
                                    </td>

                                    <!-- En utilisation -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-lg font-bold text-xs {{ $enUtil > 0 ? 'bg-blue-50 text-primary border border-blue-200' : 'bg-surface text-content-secondary border border-border/60' }}">
                                            {{ $enUtil }}
                                        </span>
                                    </td>

                                    <!-- En panne -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-lg font-bold text-xs {{ $enPanne > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-surface text-content-secondary border border-border/60' }}">
                                            {{ $enPanne }}
                                        </span>
                                    </td>

                                    <!-- Total -->
                                    <td class="py-3.5 px-4 text-center font-extrabold text-content text-sm">
                                        {{ $total }}
                                    </td>

                                    <!-- Actions (+, −, Changer la situation) -->
                                    <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Bouton Vert (+) : Entrée en réserve -->
                                            <button
                                                type="button"
                                                @click="openEntreeModal({
                                                    id: {{ $produit->id }},
                                                    nom: '{{ addslashes($nomProd) }}',
                                                    ref: '{{ $produit->reference }}',
                                                    en_stock: {{ $enStock }},
                                                    total: {{ $total }}
                                                })"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs transition-transform active:scale-95 cursor-pointer"
                                                title="Entrée en réserve (+)"
                                            >
                                                <x-ui.icon name="plus" class="w-4 h-4" />
                                            </button>

                                            <!-- Bouton Orange (−) : Sortie définitive -->
                                            <button
                                                type="button"
                                                @click="openSortieModal({
                                                    id: {{ $produit->id }},
                                                    nom: '{{ addslashes($nomProd) }}',
                                                    ref: '{{ $produit->reference }}',
                                                    en_stock: {{ $enStock }}
                                                })"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-accent hover:bg-accent-light text-white shadow-2xs transition-transform active:scale-95 disabled:opacity-35 disabled:cursor-not-allowed cursor-pointer"
                                                {{ $enStock <= 0 ? 'disabled' : '' }}
                                                title="Sortie définitive (−)"
                                            >
                                                <x-ui.icon name="minus" class="w-4 h-4" />
                                            </button>

                                            <!-- Bouton Bleu : Changer la situation -->
                                            <button
                                                type="button"
                                                @click="openSituationModal({
                                                    id: {{ $produit->id }},
                                                    nom: '{{ addslashes($nomProd) }}',
                                                    ref: '{{ $produit->reference }}',
                                                    en_stock: {{ $enStock }},
                                                    en_utilisation: {{ $enUtil }},
                                                    en_panne: {{ $enPanne }},
                                                    total: {{ $total }}
                                                })"
                                                class="inline-flex items-center justify-center px-2.5 h-8 rounded-lg bg-primary hover:bg-primary-light text-white font-medium text-xs shadow-2xs transition-transform active:scale-95 cursor-pointer gap-1"
                                                title="Changer la situation (En stock ↔ En utilisation ↔ En panne)"
                                            >
                                                <x-ui.icon name="refresh-cw" class="w-3.5 h-3.5" />
                                                <span>Situation</span>
                                            </button>

                                            <!-- Demande par Gmail -->
                                            <a
                                                href="{{ route('it.demandes.create', ['produit_id' => $produit->id]) }}"
                                                class="p-1.5 text-content-secondary hover:text-accent hover:bg-accent/10 rounded-lg transition-colors"
                                                title="Demander du matériel"
                                            >
                                                <x-ui.icon name="mail" class="w-4 h-4" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($produits->hasPages())
                    <div class="p-4 border-t border-border">
                        {{ $produits->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- ========================================================= -->
        <!-- MODALE 1 : ENTRÉE EN STOCK (+)                            -->
        <!-- ========================================================= -->
        <div
            x-show="entreeModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="entreeModalOpen" class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="entreeModalOpen = false"></div>

                <div
                    x-show="entreeModalOpen"
                    class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-border z-10"
                >
                    <div class="flex items-center justify-between pb-4 border-b border-border mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                <x-ui.icon name="plus" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content">Entrée de stock</h3>
                                <p class="text-xs text-content-secondary">Réception de matériel en réserve</p>
                            </div>
                        </div>
                        <button type="button" @click="entreeModalOpen = false" class="text-content-secondary hover:text-content">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <form method="POST" action="{{ route('it.stocks.entree.store') }}">
                        @csrf
                        <input type="hidden" name="produit_id" :value="entreeProduct.id">

                        <div class="space-y-4 text-left">
                            <div class="bg-surface/50 p-3.5 rounded-xl border border-border">
                                <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Matériel sélectionné</span>
                                <span class="text-sm font-bold text-content block mt-0.5" x-text="entreeProduct.nom"></span>
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
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Motif</label>
                                <input
                                    type="text"
                                    name="motif"
                                    value="Livraison fournisseur / Réception"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                                >
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-border">
                            <button type="button" @click="entreeModalOpen = false" class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface">
                                Annuler
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs">
                                Confirmer l'entrée
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODALE 2 : SORTIE DÉFINITIVE (−)                          -->
        <!-- ========================================================= -->
        <div
            x-show="sortieModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="sortieModalOpen" class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="sortieModalOpen = false"></div>

                <div
                    x-show="sortieModalOpen"
                    class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-border z-10"
                >
                    <div class="flex items-center justify-between pb-4 border-b border-border mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-accent flex items-center justify-center font-bold">
                                <x-ui.icon name="minus" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content">Sortie définitive</h3>
                                <p class="text-xs text-content-secondary">Mise au rebut, perte ou sortie définitive du stock</p>
                            </div>
                        </div>
                        <button type="button" @click="sortieModalOpen = false" class="text-content-secondary hover:text-content">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <form method="POST" action="{{ route('it.stocks.sortie.store') }}">
                        @csrf
                        <input type="hidden" name="produit_id" :value="sortieProduct.id">

                        <div class="space-y-4 text-left">
                            <div class="bg-surface/50 p-3.5 rounded-xl border border-border">
                                <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Matériel</span>
                                <span class="text-sm font-bold text-content block mt-0.5" x-text="sortieProduct.nom"></span>
                                <div class="flex items-center justify-between mt-1 text-xs">
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
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Motif justifié</label>
                                <input
                                    type="text"
                                    name="motif"
                                    placeholder="Ex: Mise au rebut matériel obsolète, Perte confirmée..."
                                    required
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20"
                                >
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-border">
                            <button type="button" @click="sortieModalOpen = false" class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface">
                                Annuler
                            </button>
                            <button type="submit" class="px-5 py-2 bg-accent hover:bg-accent-light text-white rounded-xl text-xs font-bold shadow-xs">
                                Confirmer la sortie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODALE 3 : CHANGER LA SITUATION DU MATÉRIEL               -->
        <!-- En stock ↔ En utilisation ↔ En panne                      -->
        <!-- ========================================================= -->
        <div
            x-show="situationModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="situationModalOpen" class="fixed inset-0 bg-content/50 backdrop-blur-xs" @click="situationModalOpen = false"></div>

                <div
                    x-show="situationModalOpen"
                    class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-border z-10"
                >
                    <div class="flex items-center justify-between pb-4 border-b border-border mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                                <x-ui.icon name="refresh-cw" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content">Changer la situation</h3>
                                <p class="text-xs text-content-secondary">Répartition interne : Réserve, Utilisation, Panne</p>
                            </div>
                        </div>
                        <button type="button" @click="situationModalOpen = false" class="text-content-secondary hover:text-content">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <form method="POST" action="{{ route('it.stocks.situation.store') }}">
                        @csrf
                        <input type="hidden" name="produit_id" :value="situationProduct.id">

                        <div class="space-y-4 text-left">
                            <!-- Matériel -->
                            <div class="bg-surface/50 p-3.5 rounded-xl border border-border">
                                <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Matériel</span>
                                <span class="text-sm font-bold text-content block mt-0.5" x-text="situationProduct.nom"></span>
                                <div class="flex items-center gap-3 mt-1.5 text-xs text-content-secondary flex-wrap">
                                    <span>Stock réserve : <strong class="text-emerald-700" x-text="situationProduct.en_stock || 0"></strong></span>
                                    <span>&bull;</span>
                                    <span>En utilisation : <strong class="text-primary" x-text="situationProduct.en_utilisation || 0"></strong></span>
                                    <span>&bull;</span>
                                    <span>En panne : <strong class="text-rose-600" x-text="situationProduct.en_panne || 0"></strong></span>
                                </div>
                            </div>

                            <!-- Situations Départ & Arrivée -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Situation actuelle</label>
                                    <select
                                        name="situation_depart"
                                        x-model="situationDepart"
                                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs font-semibold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
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
                                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs font-semibold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="en_utilisation">En utilisation</option>
                                        <option value="en_stock">En stock (réserve)</option>
                                        <option value="en_panne">En panne</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Quantité avec vérification du max disponible -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider">Quantité</label>
                                    <span class="text-[11px] font-semibold text-content-secondary">
                                        Disponible : <span class="font-bold text-primary" x-text="maxDisponible + ' unité(s)'"></span>
                                    </span>
                                </div>
                                <input
                                    type="number"
                                    name="quantite"
                                    x-model="situationQuantite"
                                    min="1"
                                    :max="maxDisponible"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                                >
                            </div>

                            <!-- Motif -->
                            <div>
                                <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">Motif</label>
                                <input
                                    type="text"
                                    name="motif"
                                    x-model="situationMotif"
                                    placeholder="Ex: Mise en service production, Défaillance matériel, Retour en réserve..."
                                    required
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                                >
                            </div>

                            <!-- Confirmation spécifique pour remise en service d'un matériel en panne -->
                            <div
                                x-show="situationDepart === 'en_panne'"
                                class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl"
                            >
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
                        <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-border">
                            <button
                                type="button"
                                @click="situationModalOpen = false"
                                class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                :disabled="!situationValide"
                                class="px-5 py-2 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-bold shadow-xs disabled:opacity-40 disabled:cursor-not-allowed"
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
