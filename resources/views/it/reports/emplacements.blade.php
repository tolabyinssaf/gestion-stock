<x-layouts.dashboard title="Rapport : Stock par emplacement" :breadcrumbs="['Rapports & Exports' => route('it.reports.index'), 'Stock par emplacement' => null]">

    <div>
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="map-pin" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Rapport — Stock par emplacement</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Consultez les quantités de matériel disponibles dans chaque emplacement de stockage.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="file-spreadsheet" class="w-4 h-4" />
                    Exporter Excel (.csv)
                </a>
                <a
                    href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="printer" class="w-4 h-4" />
                    Imprimer / PDF
                </a>
            </div>
        </div>

        <!-- Filters Form -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('it.reports.emplacements') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Matériel, référence..."
                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div>
                    <select name="emplacement_id" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous les emplacements</option>
                        @foreach ($emplacements as $emp)
                            <option value="{{ $emp->id }}" {{ request('emplacement_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->nom }} ({{ $emp->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="category_id" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-light transition-colors">
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'emplacement_id', 'category_id']))
                        <a href="{{ route('it.reports.emplacements') }}" class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors" title="Réinitialiser">
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <x-ui.table
            :headers="['Emplacement', 'Matériel', 'Référence', 'Catégorie', 'Quantité disponible']"
            :isEmpty="$stocksEmplacements->isEmpty()"
            emptyIcon="map-pin"
            emptyTitle="Aucune ligne de stock trouvée"
            emptyDescription="Aucun matériel avec du stock positif ne correspond aux critères sélectionnés."
        >
            @foreach ($stocksEmplacements as $stk)
                <tr class="hover:bg-surface/50 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="font-bold text-xs text-content">{{ $stk->emplacement->nom ?? '-' }}</div>
                        <div class="text-[11px] font-mono text-content-secondary">{{ $stk->emplacement->code ?? '' }}</div>
                    </td>
                    <td class="px-5 py-3 text-xs">
                        <div class="font-bold text-content">{{ $stk->produit->nom ?? $stk->produit->designation ?? '-' }}</div>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-content-secondary whitespace-nowrap">
                        {{ $stk->produit->reference ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-xs text-content-secondary whitespace-nowrap">
                        {{ $stk->produit->categorie->nom ?? 'Non classé' }}
                    </td>
                    <td class="px-5 py-3 text-xs font-bold text-emerald-700 whitespace-nowrap">
                        {{ $stk->quantite }} unité(s)
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $stocksEmplacements->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>
    </div>

</x-layouts.dashboard>
