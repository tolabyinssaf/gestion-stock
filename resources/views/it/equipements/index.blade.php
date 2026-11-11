<x-layouts.dashboard title="Équipements informatiques" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), 'Équipements individuels' => null]">

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
                    <x-ui.icon name="laptop" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Équipements informatiques</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Suivi individuel et traçabilité de chaque unité physique du parc (ordinateurs, écrans, serveurs, périphériques sérialisés).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center">
                <a
                    href="{{ route('it.products.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                    Gestion du matériel
                </a>
                <a
                    href="{{ route('it.equipements.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    Ajouter un équipement
                </a>
            </div>
        </div>

        <!-- KPIs Cards (5 indicators) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- 1. Total -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="hard-drive" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-content-secondary uppercase tracking-wider truncate">Total parc</p>
                    <p class="text-xl font-extrabold text-content mt-0.5">{{ $totalEquipements }}</p>
                    <p class="text-[10px] text-content-secondary truncate">Unités répertoriées</p>
                </div>
            </div>

            <!-- 2. Disponibles -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="check-circle" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-content-secondary uppercase tracking-wider truncate">Disponibles</p>
                    <p class="text-xl font-extrabold text-emerald-600 mt-0.5">{{ $disponiblesCount }}</p>
                    <p class="text-[10px] text-content-secondary truncate">Prêts au déploiement</p>
                </div>
            </div>

            <!-- 3. En utilisation -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-primary-light flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="laptop" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-content-secondary uppercase tracking-wider truncate">En service</p>
                    <p class="text-xl font-extrabold text-primary-light mt-0.5">{{ $enUtilisationCount }}</p>
                    <p class="text-[10px] text-content-secondary truncate">En cours d'utilisation</p>
                </div>
            </div>

            <!-- 4. En panne -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-content-secondary uppercase tracking-wider truncate">En panne</p>
                    <p class="text-xl font-extrabold text-rose-600 mt-0.5">{{ $enPanneCount }}</p>
                    <p class="text-[10px] text-content-secondary truncate">À diagnostiquer / réparer</p>
                </div>
            </div>

            <!-- 5. Hors service -->
            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="wrench" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-content-secondary uppercase tracking-wider truncate">Hors service</p>
                    <p class="text-xl font-extrabold text-slate-600 mt-0.5">{{ $horsServiceCount }}</p>
                    <p class="text-[10px] text-content-secondary truncate">Matériels déclassés</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl border border-border p-4 mb-6 shadow-xs">
            <form method="GET" action="{{ route('it.equipements.index') }}" class="space-y-3">
                <!-- Search bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Rechercher par code inventaire, numéro de série, produit, marque, modèle..."
                        class="block w-full pl-10 pr-4 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    />
                </div>

                <!-- Filters Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Catégorie -->
                    <div>
                        <select
                            name="categorie_id"
                            onchange="this.form.submit()"
                            class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Toutes catégories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string)$categorieId === (string)$cat->id ? 'selected' : '' }}>
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Modèle / Produit -->
                    <div>
                        <select
                            name="produit_id"
                            onchange="this.form.submit()"
                            class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Tous modèles</option>
                            @foreach ($produits as $prod)
                                <option value="{{ $prod->id }}" {{ (string)$produitId === (string)$prod->id ? 'selected' : '' }}>
                                    {{ $prod->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- État physique -->
                    <div>
                        <select
                            name="etat_physique"
                            onchange="this.form.submit()"
                            class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Tous états physiques</option>
                            @foreach (\App\Models\Equipement::ETATS_PHYSIQUES as $key => $label)
                                <option value="{{ $key }}" {{ $etatPhysique === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut d'utilisation -->
                    <div>
                        <select
                            name="statut_utilisation"
                            onchange="this.form.submit()"
                            class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Tous statuts d'utilisation</option>
                            @foreach (\App\Models\Equipement::STATUTS_UTILISATION as $key => $label)
                                <option value="{{ $key }}" {{ $statutUtilisation === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Emplacement -->
                    <div>
                        <select
                            name="emplacement_id"
                            onchange="this.form.submit()"
                            class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Tous emplacements</option>
                            @foreach ($emplacements as $loc)
                                <option value="{{ $loc->id }}" {{ (string)$emplacementId === (string)$loc->id ? 'selected' : '' }}>
                                    {{ $loc->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut archivage & Boutons -->
                    <div class="flex items-center gap-2">
                        <select
                            name="statut"
                            onchange="this.form.submit()"
                            class="block w-full py-2 px-3 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Tous archivages</option>
                            <option value="actif" {{ $statut === 'actif' ? 'selected' : '' }}>Actifs</option>
                            <option value="archive" {{ $statut === 'archive' ? 'selected' : '' }}>Archivés</option>
                        </select>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center p-2 bg-primary hover:bg-primary-light text-white rounded-xl transition-colors flex-shrink-0"
                            title="Filtrer"
                        >
                            <x-ui.icon name="filter" class="w-4 h-4" />
                        </button>

                        @if ($search || $produitId || $categorieId || $etatPhysique || $statutUtilisation || $emplacementId || $statut)
                            <a
                                href="{{ route('it.equipements.index') }}"
                                class="inline-flex items-center justify-center p-2 border border-border bg-white text-content-secondary hover:text-content hover:bg-surface rounded-xl transition-colors flex-shrink-0"
                                title="Réinitialiser"
                            >
                                <x-ui.icon name="refresh-cw" class="w-4 h-4" />
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Equipments Table -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden">
            @if ($equipements->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        <x-ui.icon name="laptop" class="w-8 h-8" />
                    </div>
                    <h3 class="text-base font-bold text-content">Aucun équipement trouvé</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">
                        @if ($search || $produitId || $categorieId || $etatPhysique || $statutUtilisation || $emplacementId || $statut)
                            Aucun équipement ne correspond aux filtres appliqués. Essayez d'ajuster ou de réinitialiser la recherche.
                        @else
                            Votre parc informatique ne contient encore aucun équipement unitaire. Enregistrez votre premier matériel pour commencer le suivi.
                        @endif
                    </p>
                    <div class="mt-5">
                        @if ($search || $produitId || $categorieId || $etatPhysique || $statutUtilisation || $emplacementId || $statut)
                            <a href="{{ route('it.equipements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-content text-xs font-semibold rounded-xl border border-border hover:bg-slate-100 transition-colors">
                                Réinitialiser les filtres
                            </a>
                        @else
                            <a
                                href="{{ route('it.equipements.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-light text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                            >
                                <x-ui.icon name="plus" class="w-4 h-4" />
                                Enregistrer un équipement
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border bg-surface/50 text-[11px] font-bold text-content-secondary uppercase tracking-wider">
                                <th class="py-3.5 px-6">Code Inventaire</th>
                                <th class="py-3.5 px-6">Équipement / Modèle</th>
                                <th class="py-3.5 px-6">Numéro de série</th>
                                <th class="py-3.5 px-6">Catégorie</th>
                                <th class="py-3.5 px-6 text-center">État physique</th>
                                <th class="py-3.5 px-6 text-center">Statut d'utilisation</th>
                                <th class="py-3.5 px-6">Emplacement</th>
                                <th class="py-3.5 px-6 text-center">Date d'entrée</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @foreach ($equipements as $eq)
                                <tr class="hover:bg-surface/40 transition-colors {{ $eq->isArchive() ? 'opacity-60 bg-slate-50/50' : '' }}">
                                    <!-- Code inventaire -->
                                    <td class="py-4 px-6">
                                        <a href="{{ route('it.equipements.show', $eq) }}" class="inline-flex items-center gap-1.5 font-mono font-bold text-primary hover:underline">
                                            <code class="px-2 py-1 rounded-md bg-surface text-[11px] font-semibold text-content border border-border">
                                                {{ $eq->code_inventaire }}
                                            </code>
                                        </a>
                                        @if ($eq->isArchive())
                                            <span class="block text-[10px] text-slate-500 font-semibold mt-0.5">Archivé</span>
                                        @endif
                                    </td>

                                    <!-- Équipement / Modèle -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            @if ($eq->produit->photo_url)
                                                <img
                                                    src="{{ $eq->produit->photo_url }}"
                                                    alt="{{ $eq->produit->nom }}"
                                                    class="w-10 h-10 rounded-xl object-cover border border-border flex-shrink-0"
                                                />
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-surface border border-border flex items-center justify-center text-content-secondary flex-shrink-0">
                                                    <x-ui.icon :name="$eq->produit->categorie->icon_name ?? 'laptop'" class="w-5 h-5" />
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('it.equipements.show', $eq) }}" class="font-bold text-content hover:text-primary transition-colors block text-sm">
                                                    {{ $eq->produit->nom }}
                                                </a>
                                                <div class="flex items-center gap-1.5 text-[11px] text-content-secondary mt-0.5">
                                                    @if ($eq->produit->marque)
                                                        <span class="font-medium text-content">{{ $eq->produit->marque }}</span>
                                                    @endif
                                                    @if ($eq->produit->marque && $eq->produit->modele)
                                                        <span>&bull;</span>
                                                    @endif
                                                    @if ($eq->produit->modele)
                                                        <span>{{ $eq->produit->modele }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Numéro de série -->
                                    <td class="py-4 px-6">
                                        @if ($eq->numero_serie)
                                            <code class="font-mono text-[11px] text-content px-1.5 py-0.5 rounded bg-surface border border-border">
                                                {{ $eq->numero_serie }}
                                            </code>
                                        @else
                                            <span class="text-content-secondary italic text-[11px]">Non renseigné</span>
                                        @endif
                                    </td>

                                    <!-- Catégorie -->
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-surface text-[11px] font-medium text-content border border-border">
                                            <x-ui.icon :name="$eq->produit->categorie->icon_name ?? 'layers'" class="w-3.5 h-3.5 text-content-secondary" />
                                            {{ $eq->produit->categorie->nom ?? 'Non classé' }}
                                        </span>
                                    </td>

                                    <!-- État physique -->
                                    <td class="py-4 px-6 text-center">
                                        @if ($eq->isBonEtat())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Bon état
                                            </span>
                                        @elseif ($eq->etat_physique === \App\Models\Equipement::ETAT_A_VERIFIER)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                À vérifier
                                            </span>
                                        @elseif ($eq->isEnPanne())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                En panne
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                Hors service
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Statut d'utilisation -->
                                    <td class="py-4 px-6 text-center">
                                        @if ($eq->isDisponible())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Disponible
                                            </span>
                                        @elseif ($eq->isEnUtilisation())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-primary-light border border-blue-200">
                                                En utilisation
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                Retiré du service
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Emplacement -->
                                    <td class="py-4 px-6">
                                        @if ($eq->emplacement)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-content">
                                                <x-ui.icon name="map-pin" class="w-3.5 h-3.5 text-accent" />
                                                {{ $eq->emplacement->nom }}
                                            </span>
                                        @else
                                            <span class="text-content-secondary italic text-[11px]">Non assigné</span>
                                        @endif
                                    </td>

                                    <!-- Date d'entrée -->
                                    <td class="py-4 px-6 text-center text-content-secondary text-[11px]">
                                        {{ $eq->date_entree ? $eq->date_entree->format('d/m/Y') : '—' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- View -->
                                            <a
                                                href="{{ route('it.equipements.show', $eq) }}"
                                                class="p-2 rounded-lg text-content-secondary hover:text-primary hover:bg-primary/5 transition-colors"
                                                title="Consulter la fiche"
                                            >
                                                <x-ui.icon name="eye" class="w-4 h-4" />
                                            </a>

                                            <!-- Edit -->
                                            <a
                                                href="{{ route('it.equipements.edit', $eq) }}"
                                                class="p-2 rounded-lg text-content-secondary hover:text-primary hover:bg-primary/5 transition-colors"
                                                title="Modifier"
                                            >
                                                <x-ui.icon name="pencil" class="w-4 h-4" />
                                            </a>

                                            <!-- Toggle Status -->
                                            <form method="POST" action="{{ route('it.equipements.toggle-status', $eq) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="p-2 rounded-lg text-content-secondary hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                                    title="{{ $eq->isActif() ? 'Archiver l\'équipement' : 'Réactiver l\'équipement' }}"
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

                @if ($equipements->hasPages())
                    <div class="p-4 border-t border-border">
                        {{ $equipements->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-layouts.dashboard>
