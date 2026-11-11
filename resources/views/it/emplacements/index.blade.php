<x-layouts.dashboard title="Emplacements de stockage" :breadcrumbs="['Gestion du stock' => route('it.stocks.index'), 'Emplacements' => null]">

    <div
        x-data="{
            isModalOpen: false,
            isEditing: false,
            modalTitle: 'Ajouter un emplacement',
            formAction: '{{ route('it.emplacements.store') }}',
            formMethod: 'POST',
            formData: {
                id: '',
                nom: '',
                code: '',
                site: '',
                description: '',
                est_actif: true
            },
            openCreateModal() {
                this.isEditing = false;
                this.modalTitle = 'Ajouter un emplacement de stockage';
                this.formAction = '{{ route('it.emplacements.store') }}';
                this.formMethod = 'POST';
                this.formData = { id: '', nom: '', code: '', site: '', description: '', est_actif: true };
                this.isModalOpen = true;
            },
            openEditModal(emplacement) {
                this.isEditing = true;
                this.modalTitle = 'Modifier l\'emplacement';
                this.formAction = '/it/emplacements/' + emplacement.id;
                this.formMethod = 'PUT';
                this.formData = {
                    id: emplacement.id,
                    nom: emplacement.nom,
                    code: emplacement.code,
                    site: emplacement.site || '',
                    description: emplacement.description || '',
                    est_actif: Boolean(emplacement.est_actif)
                };
                this.isModalOpen = true;
            },
            closeModal() {
                this.isModalOpen = false;
            }
        }"
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

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="map-pin" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Emplacements de stockage physique</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Gestion des magasins, armoires, réserves IT et bureaux pour cartographier où se trouve chaque équipement ou pièce détachée.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center">
                <a href="{{ route('it.stocks.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors">
                    <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                    Gestion du stock
                </a>
                <button
                    type="button"
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    Ajouter un emplacement
                </button>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('it.emplacements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Search -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher nom, code, site..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- Site -->
                <div>
                    <select
                        name="site"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les sites</option>
                        @foreach ($sites as $siteName)
                            <option value="{{ $siteName }}" {{ request('site') === $siteName ? 'selected' : '' }}>
                                {{ $siteName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Statut & submit -->
                <div class="flex items-center gap-2">
                    <select
                        name="est_actif"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('est_actif') === '1' ? 'selected' : '' }}>Actifs uniquement</option>
                        <option value="0" {{ request('est_actif') === '0' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                    <button
                        type="submit"
                        class="px-4 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-semibold transition-colors flex-shrink-0"
                    >
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'site', 'est_actif']))
                        <a
                            href="{{ route('it.emplacements.index') }}"
                            class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Emplacements Table -->
        <x-ui.table
            :headers="['Emplacement', 'Code', 'Site / Bâtiment', 'Équipements présents', 'Lignes de stock', 'Statut', 'Actions']"
            :isEmpty="$emplacements->isEmpty()"
            emptyIcon="map-pin"
            emptyTitle="Aucun emplacement trouvé"
            emptyDescription="Créez votre premier emplacement physique (armoire, bureau IT, salle serveur) pour organiser vos matériels."
        >
            @foreach ($emplacements as $emp)
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Nom -->
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                                <x-ui.icon name="map-pin" class="w-4 h-4" />
                            </div>
                            <div>
                                <a href="{{ route('it.emplacements.show', $emp) }}" class="font-semibold text-content hover:text-primary transition-colors text-xs">
                                    {{ $emp->nom }}
                                </a>
                                @if($emp->description)
                                    <p class="text-[11px] text-content-secondary mt-0.5 line-clamp-1">
                                        {{ $emp->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Code -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-md bg-surface text-content border border-border">
                            {{ $emp->code }}
                        </span>
                    </td>

                    <!-- Site -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-content-secondary">
                        {{ $emp->site ?? '-' }}
                    </td>

                    <!-- Équipements individuels -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        <span class="font-bold text-content">{{ $emp->equipements_count }}</span>
                        <span class="text-content-secondary">unités</span>
                    </td>

                    <!-- Lignes de stock -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        <span class="font-bold text-content">{{ $emp->stocks_count }}</span>
                        <span class="text-content-secondary">références</span>
                    </td>

                    <!-- Statut -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        <form method="POST" action="{{ route('it.emplacements.toggle-status', $emp) }}">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold transition-colors {{ $emp->est_actif ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' }}"
                                title="Cliquer pour basculer"
                            >
                                <span class="w-1.5 h-1.5 rounded-full {{ $emp->est_actif ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $emp->est_actif ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                    </td>

                    <!-- Actions -->
                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                        <div class="flex items-center justify-end gap-1.5">
                            <a
                                href="{{ route('it.emplacements.show', $emp) }}"
                                class="p-1.5 text-content-secondary hover:text-primary hover:bg-surface rounded-lg transition-colors"
                                title="Voir le contenu"
                            >
                                <x-ui.icon name="eye" class="w-4 h-4" />
                            </a>

                            <button
                                type="button"
                                @click="openEditModal({{ json_encode($emp) }})"
                                class="p-1.5 text-content-secondary hover:text-accent hover:bg-surface rounded-lg transition-colors"
                                title="Modifier"
                            >
                                <x-ui.icon name="edit" class="w-4 h-4" />
                            </button>

                            @if($emp->equipements_count === 0 && $emp->stocks_count === 0)
                                <form method="POST" action="{{ route('it.emplacements.destroy', $emp) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet emplacement ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                                        title="Supprimer"
                                    >
                                        <x-ui.icon name="trash" class="w-4 h-4" />
                                    </button>
                                </form>
                            @else
                                <span class="p-1.5 text-slate-300 cursor-not-allowed" title="Cet emplacement contient du matériel et ne peut être supprimé">
                                    <x-ui.icon name="lock" class="w-4 h-4" />
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $emplacements->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>

        <!-- Modal Création / Édition -->
        <div
            x-show="isModalOpen"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div
                    x-show="isModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                    @click="closeModal()"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    x-show="isModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-border"
                >
                    <form :action="formAction" method="POST">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-6">
                            <div class="flex items-center justify-between pb-4 border-b border-border">
                                <h3 class="text-base font-bold text-content" x-text="modalTitle"></h3>
                                <button type="button" @click="closeModal()" class="text-content-secondary hover:text-content">
                                    <x-ui.icon name="x" class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="space-y-4 py-4">
                                <!-- Nom -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Nom de l'emplacement <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="nom"
                                        x-model="formData.nom"
                                        required
                                        placeholder="ex: Magasin IT Principal, Armoire A2..."
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                    >
                                </div>

                                <!-- Code -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Code d'identification <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="code"
                                        x-model="formData.code"
                                        required
                                        placeholder="ex: MAG-01, ARM-A2, BUR-104..."
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                    >
                                </div>

                                <!-- Site -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Site / Bâtiment / Étage
                                    </label>
                                    <input
                                        type="text"
                                        name="site"
                                        x-model="formData.site"
                                        placeholder="ex: Siège Social - Bâtiment B, Étage 2..."
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                    >
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-1.5">
                                        Description
                                    </label>
                                    <textarea
                                        name="description"
                                        x-model="formData.description"
                                        rows="2"
                                        placeholder="Précisions sur l'accès, le type de matériel stocké..."
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                    ></textarea>
                                </div>

                                <!-- Statut actif -->
                                <div class="flex items-center gap-2 pt-2">
                                    <input
                                        type="checkbox"
                                        id="est_actif"
                                        name="est_actif"
                                        value="1"
                                        x-model="formData.est_actif"
                                        class="w-4 h-4 rounded border-border text-primary focus:ring-primary/20"
                                    >
                                    <label for="est_actif" class="text-xs font-medium text-content cursor-pointer">
                                        Emplacement actif (disponible pour les mouvements)
                                    </label>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-border flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    @click="closeModal()"
                                    class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    class="px-5 py-2 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-semibold shadow-xs transition-colors"
                                >
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.dashboard>
