<x-layouts.dashboard title="Historique des opérations" :breadcrumbs="['Historique' => null]">

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
                    <h2 class="text-xl font-bold text-content tracking-tight">Historique des opérations</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Consultation de toutes les opérations effectuées : entrées, sorties, transferts et ajustements de stock.
                    </p>
                </div>
            </div>

            <!-- Action shortcuts -->
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
                <!-- Search -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Réf, motif, bénéficiaire, n° série..."
                        class="w-full pl-10 pr-4 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- Type -->
                <div>
                    <select
                        name="type"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="">Tous types</option>
                        <option value="entree" {{ request('type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie" {{ request('type') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                        <option value="transfert" {{ request('type') === 'transfert' ? 'selected' : '' }}>Transfert</option>
                        <option value="correction" {{ request('type') === 'correction' ? 'selected' : '' }}>Correction</option>
                    </select>
                </div>

                <!-- Emplacement -->
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

                <!-- Date début -->
                <div>
                    <input
                        type="date"
                        name="date_debut"
                        value="{{ request('date_debut') }}"
                        title="Date début"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- Date fin & submit -->
                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="date_fin"
                        value="{{ request('date_fin') }}"
                        title="Date fin"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                    <button
                        type="submit"
                        class="px-4 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-semibold transition-colors flex-shrink-0"
                    >
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'type', 'emplacement_id', 'date_debut', 'date_fin']))
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
            :headers="['Référence', 'Type', 'Article / Équipement', 'Qté', 'Source &rarr; Destination', 'Motif / Bénéficiaire', 'Opérateur & Date', 'Actions']"
            :isEmpty="$mouvements->isEmpty()"
            emptyIcon="history"
            emptyTitle="Aucun mouvement trouvé"
            emptyDescription="Aucune opération de stock ne correspond à vos filtres actuels."
        >
            @foreach ($mouvements as $mvt)
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Référence -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        <a href="{{ route('it.mouvements.show', $mvt) }}" class="font-mono text-xs font-bold text-primary hover:underline">
                            {{ $mvt->reference }}
                        </a>
                    </td>

                    <!-- Type -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        @if ($mvt->type === 'entree')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-ui.icon name="arrow-down-left" class="w-3.5 h-3.5 text-emerald-600" />
                                Entrée
                            </span>
                        @elseif ($mvt->type === 'sortie')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary/10 text-primary border border-primary/20">
                                <x-ui.icon name="arrow-up-right" class="w-3.5 h-3.5" />
                                Sortie
                            </span>
                        @elseif ($mvt->type === 'transfert')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <x-ui.icon name="arrow-right-left" class="w-3.5 h-3.5" />
                                Transfert
                            </span>
                        @elseif ($mvt->type === 'correction')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-accent/10 text-accent border border-accent/20">
                                <x-ui.icon name="wrench" class="w-3.5 h-3.5" />
                                Correction
                            </span>
                        @endif
                    </td>

                    <!-- Article / Équipement -->
                    <td class="px-5 py-4">
                        <div class="text-xs font-semibold text-content">
                            {{ $mvt->produit->designation ?? 'N/A' }}
                        </div>
                        @if($mvt->equipement)
                            <div class="text-[11px] text-accent mt-0.5 font-mono">
                                S/N: {{ $mvt->equipement->numero_serie }}
                            </div>
                        @else
                            <div class="text-[11px] text-content-secondary mt-0.5">
                                Réf: {{ $mvt->produit->reference ?? '-' }}
                            </div>
                        @endif
                    </td>

                    <!-- Quantité -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs font-bold text-content">
                        @if($mvt->type === 'entree')
                            <span class="text-emerald-600 font-bold">+{{ $mvt->quantite }}</span>
                        @elseif($mvt->type === 'sortie')
                            <span class="text-rose-600 font-bold">-{{ $mvt->quantite }}</span>
                        @else
                            <span>{{ $mvt->quantite }}</span>
                        @endif
                    </td>

                    <!-- Source -> Destination -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-content-secondary">
                        @if($mvt->type === 'entree')
                            <span class="text-emerald-700 font-medium">&rarr; {{ $mvt->emplacementDestination->nom ?? 'Inconnu' }}</span>
                        @elseif($mvt->type === 'sortie')
                            <span class="text-rose-700 font-medium">{{ $mvt->emplacementSource->nom ?? 'Inconnu' }} &rarr;</span>
                        @elseif($mvt->type === 'transfert')
                            <span>{{ $mvt->emplacementSource->nom ?? '-' }} &rarr; {{ $mvt->emplacementDestination->nom ?? '-' }}</span>
                        @else
                            <span>{{ $mvt->emplacementDestination->nom ?? ($mvt->emplacementSource->nom ?? '-') }}</span>
                        @endif
                    </td>

                    <!-- Motif / Bénéficiaire -->
                    <td class="px-5 py-4 text-xs">
                        <div class="text-content font-medium truncate max-w-xs" title="{{ $mvt->motif }}">
                            {{ $mvt->motif ?? '-' }}
                        </div>
                        @if($mvt->beneficiaire)
                            <div class="text-[11px] text-content-secondary mt-0.5">
                                Bénéficiaire: <span class="font-medium text-content">{{ $mvt->beneficiaire }}</span>
                            </div>
                        @endif
                    </td>

                    <!-- Opérateur & Date -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        <div class="text-content font-medium">
                            {{ $mvt->user->name ?? 'Système' }}
                        </div>
                        <div class="text-[11px] text-content-secondary mt-0.5">
                            {{ $mvt->date_mouvement ? $mvt->date_mouvement->format('d/m/Y H:i') : '-' }}
                        </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                        <a
                            href="{{ route('it.mouvements.show', $mvt) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-border bg-white text-xs font-medium text-content hover:bg-surface transition-colors"
                        >
                            <x-ui.icon name="eye" class="w-3.5 h-3.5 text-content-secondary" />
                            Détails
                        </a>
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
