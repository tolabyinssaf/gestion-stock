<x-layouts.dashboard title="Historique" :breadcrumbs="['Historique' => null]">

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

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="history" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Historique</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Retrouvez les entrées et sorties de matériel.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <a
                    href="{{ route('it.stocks.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="boxes" class="w-4 h-4 text-content-secondary" />
                    Gestion du stock
                </a>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('it.mouvements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5">
                <!-- 1. Recherche -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Recherche réf, motif..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- 2. Type d'opération -->
                <div>
                    <select
                        name="type"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous les types</option>
                        <option value="entree" {{ request('type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie_definitive" {{ request('type') === 'sortie_definitive' || request('type') === 'sortie' ? 'selected' : '' }}>Sortie définitive</option>
                        <option value="mise_utilisation" {{ request('type') === 'mise_utilisation' ? 'selected' : '' }}>Mise en utilisation</option>
                        <option value="retour_stock" {{ request('type') === 'retour_stock' ? 'selected' : '' }}>Retour en stock</option>
                        <option value="declaration_panne" {{ request('type') === 'declaration_panne' ? 'selected' : '' }}>Déclaration de panne</option>
                        <option value="remise_service" {{ request('type') === 'remise_service' ? 'selected' : '' }}>Remise en service</option>
                        <option value="transfert" {{ request('type') === 'transfert' ? 'selected' : '' }}>Transfert</option>
                        <option value="correction" {{ request('type') === 'correction' ? 'selected' : '' }}>Correction</option>
                    </select>
                </div>

                <!-- 3. Situation départ -->
                <div>
                    <select
                        name="situation_depart"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Situation départ (toutes)</option>
                        <option value="en_stock" {{ request('situation_depart') === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ request('situation_depart') === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ request('situation_depart') === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- 4. Situation arrivée -->
                <div>
                    <select
                        name="situation_arrivee"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Situation arrivée (toutes)</option>
                        <option value="en_stock" {{ request('situation_arrivee') === 'en_stock' ? 'selected' : '' }}>En stock (réserve)</option>
                        <option value="en_utilisation" {{ request('situation_arrivee') === 'en_utilisation' ? 'selected' : '' }}>En utilisation</option>
                        <option value="en_panne" {{ request('situation_arrivee') === 'en_panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>

                <!-- 5. Période (Date début & Date fin) -->
                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="date_debut"
                        value="{{ request('date_debut') }}"
                        title="Date début"
                        class="w-1/2 px-2.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                    <input
                        type="date"
                        name="date_fin"
                        value="{{ request('date_fin') }}"
                        title="Date fin"
                        class="w-1/2 px-2.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                    <button
                        type="submit"
                        class="px-3 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-semibold transition-colors flex-shrink-0 cursor-pointer"
                    >
                        <x-ui.icon name="filter" class="w-4 h-4" />
                    </button>
                    @if (request()->hasAny(['search', 'type', 'situation_depart', 'situation_arrivee', 'date_debut', 'date_fin']))
                        <a
                            href="{{ route('it.mouvements.index') }}"
                            class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Mouvements Table -->
        <x-ui.table
            :headers="['Référence', 'Opération', 'Matériel', 'Situation départ → arrivée', 'Qté', 'Motif', 'Date & Opérateur']"
            :isEmpty="$mouvements->isEmpty()"
            emptyIcon="history"
            emptyTitle="Aucun mouvement trouvé"
            emptyDescription="Aucune opération ne correspond à vos filtres actuels."
        >
            @foreach ($mouvements as $mvt)
                @php
                    $isEntree = $mvt->type === 'entree';
                    $isSortie = in_array($mvt->type, ['sortie', 'sortie_definitive']);
                    $isUtilisation = $mvt->type === 'mise_utilisation';
                    $isRetour = $mvt->type === 'retour_stock';
                    $isPanne = $mvt->type === 'declaration_panne';
                    $isRemise = $mvt->type === 'remise_service';
                @endphp
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Référence -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        <span class="font-mono text-xs font-bold text-content">
                            {{ $mvt->reference }}
                        </span>
                    </td>

                    <!-- Opération -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        @if ($isEntree)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-ui.icon name="arrow-down-left" class="w-3.5 h-3.5 text-emerald-600" />
                                Entrée
                            </span>
                        @elseif ($isSortie)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-accent/10 text-accent border border-accent/20">
                                <x-ui.icon name="arrow-up-right" class="w-3.5 h-3.5" />
                                Sortie définitive
                            </span>
                        @elseif ($isUtilisation)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-primary border border-blue-200">
                                <x-ui.icon name="laptop" class="w-3.5 h-3.5" />
                                Mise en utilisation
                            </span>
                        @elseif ($isRetour)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-ui.icon name="corner-down-left" class="w-3.5 h-3.5" />
                                Retour en stock
                            </span>
                        @elseif ($isPanne)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <x-ui.icon name="alert-octagon" class="w-3.5 h-3.5" />
                                Déclaration de panne
                            </span>
                        @elseif ($isRemise)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-ui.icon name="check-circle" class="w-3.5 h-3.5" />
                                Remise en service
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface text-content border border-border">
                                {{ $mvt->type_label ?? ucfirst($mvt->type) }}
                            </span>
                        @endif
                    </td>

                    <!-- Matériel -->
                    <td class="px-5 py-4">
                        <div class="text-xs font-semibold text-content">
                            {{ $mvt->produit->nom ?? $mvt->produit->designation ?? 'N/A' }}
                        </div>
                        <div class="text-[11px] text-content-secondary mt-0.5">
                            Réf : {{ $mvt->produit->reference ?? '-' }}
                        </div>
                    </td>

                    <!-- Situation départ -> arrivée -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        @if ($mvt->situation_depart || $mvt->situation_arrivee)
                            <div class="flex items-center gap-1.5">
                                <span class="font-medium text-content">{{ $mvt->situation_depart_label ?? '—' }}</span>
                                <span class="text-content-secondary">&rarr;</span>
                                <span class="font-bold text-primary">{{ $mvt->situation_arrivee_label ?? '—' }}</span>
                            </div>
                        @else
                            <span class="text-content-secondary">—</span>
                        @endif
                    </td>

                    <!-- Quantité -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs font-bold text-content">
                        @if($isEntree)
                            <span class="text-emerald-600 font-bold">+{{ $mvt->quantite }}</span>
                        @elseif($isSortie)
                            <span class="text-rose-600 font-bold">-{{ $mvt->quantite }}</span>
                        @else
                            <span>{{ $mvt->quantite }}</span>
                        @endif
                    </td>

                    <!-- Motif -->
                    <td class="px-5 py-4 text-xs">
                        <div class="text-content font-medium truncate max-w-xs" title="{{ $mvt->motif }}">
                            {{ $mvt->motif ?? '-' }}
                        </div>
                    </td>

                    <!-- Date & Opérateur -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        <div class="text-content font-medium">
                            {{ $mvt->user->name ?? 'Système' }}
                        </div>
                        <div class="text-[11px] text-content-secondary mt-0.5">
                            {{ $mvt->date_mouvement ? $mvt->date_mouvement->format('d/m/Y') : ($mvt->created_at ? $mvt->created_at->format('d/m/Y') : '-') }}
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $mouvements->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>
    </div>

</x-layouts.dashboard>
