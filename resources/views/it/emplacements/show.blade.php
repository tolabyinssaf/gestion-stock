<x-layouts.dashboard title="Détail de l'emplacement" :breadcrumbs="['Gestion IT' => null, 'Emplacements' => route('it.emplacements.index'), $emplacement->nom => null]">

    <div>
        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="map-pin" class="w-6 h-6" />
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-bold text-content tracking-tight">{{ $emplacement->nom }}</h2>
                        <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded-md bg-surface border border-border text-primary">
                            {{ $emplacement->code }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $emplacement->est_actif ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $emplacement->est_actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        {{ $emplacement->site ?? 'Site non spécifié' }} &bull; {{ $emplacement->description ?? 'Aucune description' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 self-start md:self-center">
                <a
                    href="{{ route('it.emplacements.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                    Retour aux emplacements
                </a>
            </div>
        </div>

        <!-- KPIs of Location -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="laptop" class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Équipements sérialisés</span>
                    <span class="text-xl font-bold text-content mt-0.5 block">{{ $equipements->count() }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Lignes de consommables</span>
                    <span class="text-xl font-bold text-content mt-0.5 block">{{ $stocks->count() }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="layers" class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Total pièces en stock</span>
                    <span class="text-xl font-bold text-emerald-700 mt-0.5 block">{{ number_format($stocks->sum('quantite')) }}</span>
                </div>
            </div>
        </div>

        <!-- Section 1: Équipements individuels localisés ici -->
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                        <x-ui.icon name="laptop" class="w-4 h-4 text-accent" />
                        Équipements individuels présents dans cet emplacement
                    </h3>
                    <p class="text-xs text-content-secondary mt-0.5">Unités physiques actuellement entreposées à cet endroit</p>
                </div>
                <span class="text-xs font-semibold text-content-secondary bg-surface px-2.5 py-1 rounded-lg border border-border">
                    {{ $equipements->count() }} unités
                </span>
            </div>

            <x-ui.table
                :headers="['Désignation du modèle', 'Numéro de série', 'Code inventaire', 'Statut', 'Actions']"
                :isEmpty="$equipements->isEmpty()"
                emptyIcon="laptop"
                emptyTitle="Aucun équipement individuel"
                emptyDescription="Aucune machine ou écran individuel n'est actuellement consigné à cet emplacement."
            >
                @foreach ($equipements as $eq)
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="text-xs font-bold text-content">{{ $eq->produit->designation ?? 'N/A' }}</div>
                            <div class="text-[11px] text-content-secondary">Réf: {{ $eq->produit->reference ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-xs font-bold text-primary">
                            {{ $eq->numero_serie }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-content-secondary">
                            {{ $eq->code_inventaire ?? '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $eq->statut_label }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                            <a
                                href="{{ route('it.equipements.show', $eq) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-border bg-white text-xs font-medium text-content hover:bg-surface transition-colors"
                            >
                                <x-ui.icon name="eye" class="w-3.5 h-3.5 text-content-secondary" />
                                Consulter
                            </a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        </div>

        <!-- Section 2: Consommables et accessoires stockés ici -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                        <x-ui.icon name="boxes" class="w-4 h-4 text-primary" />
                        Stocks de consommables & accessoires stockés ici
                    </h3>
                    <p class="text-xs text-content-secondary mt-0.5">Quantités disponibles dans ce magasin ou cette armoire</p>
                </div>
            </div>

            <x-ui.table
                :headers="['Désignation', 'Référence', 'Catégorie', 'Quantité disponible', 'Actions']"
                :isEmpty="$stocks->isEmpty()"
                emptyIcon="boxes"
                emptyTitle="Aucun consommable"
                emptyDescription="Aucune ligne de stock n'est enregistrée pour cet emplacement."
            >
                @foreach ($stocks as $stock)
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="text-xs font-bold text-content">{{ $stock->produit->designation ?? 'N/A' }}</div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-content-secondary">
                            {{ $stock->produit->reference ?? '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-xs text-content-secondary">
                            {{ $stock->produit->category->nom ?? '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-xs font-bold text-primary">
                            {{ $stock->quantite }} unité(s)
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                            <a
                                href="{{ route('it.stocks.transfert', ['produit_id' => $stock->produit_id, 'emplacement_source_id' => $emplacement->id]) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-border bg-white text-xs font-medium text-content hover:bg-surface transition-colors"
                            >
                                <x-ui.icon name="arrow-right-left" class="w-3.5 h-3.5 text-content-secondary" />
                                Transférer
                            </a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        </div>
    </div>

</x-layouts.dashboard>
