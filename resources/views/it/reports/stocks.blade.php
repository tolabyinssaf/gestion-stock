<x-layouts.dashboard title="Rapport : État des stocks" :breadcrumbs="['Rapports & Exports' => route('it.reports.index'), 'État des stocks' => null]">

    <div>
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="boxes" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Rapport — État des stocks</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Consultez les quantités disponibles et identifiez les stocks faibles et les ruptures.
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
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
            <form method="GET" action="{{ route('it.reports.stocks') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Search -->
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher nom, référence..."
                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <!-- Catégorie -->
                <div>
                    <select name="category_id" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- État du stock -->
                <div>
                    <select name="stock_alert" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous les états</option>
                        <option value="normal" {{ request('stock_alert') === 'normal' ? 'selected' : '' }}>Disponible (&gt; 1)</option>
                        <option value="faible" {{ request('stock_alert') === 'faible' ? 'selected' : '' }}>Stock faible (= 1)</option>
                        <option value="rupture" {{ request('stock_alert') === 'rupture' ? 'selected' : '' }}>Rupture (= 0)</option>
                    </select>
                </div>

                <!-- Submit & Reset -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-light transition-colors">
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'category_id', 'stock_alert']))
                        <a href="{{ route('it.reports.stocks') }}" class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors" title="Réinitialiser">
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <x-ui.table
            :headers="['Matériel', 'Catégorie', 'Quantité disponible', 'État du stock']"
            :isEmpty="$stocks->isEmpty()"
            emptyIcon="boxes"
            emptyTitle="Aucun matériel correspondant"
            emptyDescription="Aucun article en stock ne correspond à vos filtres de recherche."
        >
            @foreach ($stocks as $prod)
                @php
                    $qte = $prod->total_stock;
                @endphp
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Matériel -->
                    <td class="px-5 py-4">
                        <div class="text-xs font-bold text-content">{{ $prod->nom ?? $prod->designation ?? 'N/A' }}</div>
                        <div class="text-[11px] font-mono text-content-secondary mt-0.5">Réf: {{ $prod->reference ?? '-' }}</div>
                    </td>

                    <!-- Catégorie -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-content-secondary">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-surface text-content-secondary border border-border">
                            {{ $prod->categorie->nom ?? 'Non classé' }}
                        </span>
                    </td>

                    <!-- Quantité disponible -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs font-bold {{ $qte === 0 ? 'text-rose-600' : ($qte === 1 ? 'text-accent' : 'text-content') }}">
                        {{ $qte }} unité(s)
                    </td>

                    <!-- État du stock (Règles strictes : >1 Disponible, =1 Stock faible, =0 Rupture) -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        @if ($qte === 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Rupture
                            </span>
                        @elseif ($qte === 1)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Stock faible
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Disponible
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $stocks->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>
    </div>

</x-layouts.dashboard>
