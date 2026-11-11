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

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <div class="flex items-center gap-2 mb-2 font-semibold">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0" />
                    <span>Des erreurs sont survenues lors de la validation :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="layers" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Catégories de matériel</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Structurez le catalogue des équipements informatiques. Les catégories permettent d'organiser les références et de classer le stock de manière cohérente.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center">
                <a href="{{ route('it.products.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors">
                    <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                    Gestion du matériel
                </a>
                <button
                    type="button"
                    @click="openCreateDrawer()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    Nouvelle catégorie
                </button>
            </div>
        </div>

        <!-- KPIs Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-xl border border-border p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="layers" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider">Total catégories</p>
                    <p class="text-2xl font-extrabold text-content mt-0.5">{{ $totalCategories }}</p>
                    <p class="text-[11px] text-content-secondary mt-0.5">Typologies de matériel</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="check-circle" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider">Catégories actives</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $activeCategories }}</p>
                    <p class="text-[11px] text-content-secondary mt-0.5">Disponibles pour ajout</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="archive" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-bold text-content-secondary uppercase tracking-wider">Catégories archivées</p>
                    <p class="text-2xl font-extrabold text-slate-600 mt-0.5">{{ $archivedCategories }}</p>
                    <p class="text-[11px] text-content-secondary mt-0.5">Masquées du catalogue neuf</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl border border-border p-4 mb-6">
            <form method="GET" action="{{ route('it.categories.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <!-- Search input -->
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Rechercher par nom ou description..."
                        class="block w-full pl-10 pr-4 py-2 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    />
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-48">
                    <select
                        name="statut"
                        onchange="this.form.submit()"
                        class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
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
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-semibold rounded-xl transition-colors"
                    >
                        <x-ui.icon name="filter" class="w-3.5 h-3.5" />
                        Filtrer
                    </button>

                    @if ($search || $statut)
                        <a
                            href="{{ route('it.categories.index') }}"
                            class="inline-flex items-center justify-center gap-1 px-3 py-2 border border-border bg-white text-xs font-medium text-content-secondary hover:text-content hover:bg-surface rounded-xl transition-colors"
                            title="Réinitialiser"
                        >
                            <x-ui.icon name="refresh-cw" class="w-3.5 h-3.5" />
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden">
            @if ($categories->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        <x-ui.icon name="layers" class="w-8 h-8" />
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
                            <a href="{{ route('it.categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-content text-xs font-semibold rounded-xl border border-border hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
                            </a>
                        @else
                            <button
                                type="button"
                                @click="openCreateDrawer()"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-light text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                            >
                                <x-ui.icon name="plus" class="w-4 h-4" />
                                Créer une catégorie
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border bg-surface/50 text-[11px] font-bold text-content-secondary uppercase tracking-wider">
                                <th class="py-3.5 px-6">Catégorie</th>
                                <th class="py-3.5 px-6">Identifiant (Slug)</th>
                                <th class="py-3.5 px-6">Description</th>
                                <th class="py-3.5 px-6 text-center">Produits</th>
                                <th class="py-3.5 px-6 text-center">Statut</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @foreach ($categories as $cat)
                                <tr class="hover:bg-surface/40 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                                                <x-ui.icon :name="$cat->icon_name ?: 'layers'" class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <span class="font-bold text-content block text-sm">{{ $cat->nom }}</span>
                                                <span class="text-[10px] text-content-secondary">Créée le {{ $cat->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <code class="px-2 py-1 rounded-md bg-surface text-[11px] font-mono text-content-secondary border border-border">
                                            {{ $cat->slug }}
                                        </code>
                                    </td>
                                    <td class="py-4 px-6 max-w-xs">
                                        <span class="text-content-secondary line-clamp-2">
                                            {{ $cat->description ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a
                                            href="{{ route('it.products.index', ['category_id' => $cat->id]) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface hover:bg-primary/10 text-content-secondary hover:text-primary transition-colors text-xs font-semibold"
                                            title="Filtrer les produits de cette catégorie"
                                        >
                                            <x-ui.icon name="package" class="w-3.5 h-3.5" />
                                            {{ $cat->produits_count }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if ($cat->isActif())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Archivée
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Edit Button -->
                                            <button
                                                type="button"
                                                @click='openEditDrawer(@json($cat))'
                                                class="p-2 rounded-lg text-content-secondary hover:text-primary hover:bg-primary/5 transition-colors"
                                                title="Modifier la catégorie"
                                            >
                                                <x-ui.icon name="pencil" class="w-4 h-4" />
                                            </button>

                                            <!-- Toggle Status (Form) -->
                                            <form method="POST" action="{{ route('it.categories.toggle-status', $cat) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="p-2 rounded-lg text-content-secondary hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                                    title="{{ $cat->isActif() ? 'Archiver la catégorie' : 'Réactiver la catégorie' }}"
                                                >
                                                    <x-ui.icon name="archive" class="w-4 h-4" />
                                                </button>
                                            </form>

                                            <!-- Delete Button -->
                                            @if ($cat->produits_count === 0)
                                                <button
                                                    type="button"
                                                    @click='openDeleteModal({ id: {{ $cat->id }}, nom: "{{ addslashes($cat->nom) }}", count: {{ $cat->produits_count }} })'
                                                    class="p-2 rounded-lg text-content-secondary hover:text-red-600 hover:bg-red-50 transition-colors"
                                                    title="Supprimer la catégorie"
                                                >
                                                    <x-ui.icon name="trash-2" class="w-4 h-4" />
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    disabled
                                                    class="p-2 rounded-lg text-slate-300 cursor-not-allowed"
                                                    title="Suppression impossible : contient des produits"
                                                >
                                                    <x-ui.icon name="trash-2" class="w-4 h-4" />
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
                    <div class="p-4 border-t border-border">
                        {{ $categories->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Create / Edit Slideover Drawer -->
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

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div
                    x-show="drawerOpen"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-md bg-white border-l border-border shadow-2xl flex flex-col"
                >
                    <!-- Drawer Header -->
                    <div class="p-6 border-b border-border flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <x-ui.icon name="layers" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-content" x-text="drawerTitle"></h3>
                                <p class="text-xs text-content-secondary" x-text="isEditing ? 'Mettre à jour les informations' : 'Définir une nouvelle famille de matériel'"></p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="drawerOpen = false"
                            class="p-2 text-content-secondary hover:text-content rounded-lg hover:bg-surface transition-colors"
                        >
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Drawer Form -->
                    <form :action="formAction" method="POST" class="flex-1 flex flex-col justify-between overflow-y-auto">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-6 space-y-5">
                            <!-- Nom -->
                            <div>
                                <label for="category_nom" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Nom de la catégorie <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="category_nom"
                                    name="nom"
                                    x-model="formData.nom"
                                    required
                                    placeholder="Ex: Ordinateurs portables, Écrans..."
                                    class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                />
                            </div>

                            <!-- Icône -->
                            <div>
                                <label for="category_icon" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Icône représentative
                                </label>
                                <select
                                    id="category_icon"
                                    name="icon_name"
                                    x-model="formData.icon_name"
                                    class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
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
                                <label for="category_statut" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Statut <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="category_statut"
                                    name="statut"
                                    x-model="formData.statut"
                                    class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                >
                                    <option value="actif">Active (visible pour nouveaux produits)</option>
                                    <option value="archive">Archivée (masquée)</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="category_description" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Description informative
                                </label>
                                <textarea
                                    id="category_description"
                                    name="description"
                                    x-model="formData.description"
                                    rows="4"
                                    placeholder="Précisez le périmètre d'équipements concernés..."
                                    class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Drawer Footer -->
                        <div class="p-6 border-t border-border bg-surface/50 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                @click="drawerOpen = false"
                                class="px-4 py-2.5 border border-border rounded-xl text-xs font-semibold text-content hover:bg-white transition-colors"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                                x-text="isEditing ? 'Enregistrer les modifications' : 'Créer la catégorie'"
                            ></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>

            <div class="relative w-full max-w-md mx-auto my-12 bg-white rounded-2xl border border-border shadow-2xl p-6 overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="trash-2" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-content">Supprimer la catégorie</h3>
                        <p class="text-xs text-content-secondary mt-1">
                            Êtes-vous sûr de vouloir supprimer définitivement la catégorie
                            <strong class="text-content" x-text="categoryToDelete.nom"></strong> ?
                        </p>
                        <p class="text-[11px] text-content-secondary mt-2">
                            Cette action est irréversible. Seules les catégories sans produit associé peuvent être supprimées.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="deleteModalOpen = false"
                        class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        Annuler
                    </button>
                    <form :action="'/it/categories/' + categoryToDelete.id" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                        >
                            Confirmer la suppression
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-layouts.dashboard>
