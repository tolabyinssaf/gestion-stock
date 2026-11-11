<x-layouts.dashboard title="Rapport : Demandes de matériel" :breadcrumbs="['Rapports & Exports' => route('it.reports.index'), 'Demandes de matériel' => null]">

    <div>
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="mail" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Rapport — Demandes de matériel</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Consultez les demandes de réapprovisionnement et leur historique.
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
            <form method="GET" action="{{ route('it.reports.demandes') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Référence, matériel, motif..."
                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div>
                    <input
                        type="date"
                        name="date_debut"
                        value="{{ request('date_debut') }}"
                        placeholder="Date début"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                        title="Date de début"
                    >
                </div>

                <div>
                    <input
                        type="date"
                        name="date_fin"
                        value="{{ request('date_fin') }}"
                        placeholder="Date fin"
                        class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                        title="Date de fin"
                    >
                </div>

                <div>
                    <select name="priorite" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Toutes priorités</option>
                        <option value="normale" {{ request('priorite') === 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="urgente" {{ request('priorite') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <div>
                    <select name="statut_preparation" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous les statuts</option>
                        <option value="brouillon" {{ request('statut_preparation') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="prete" {{ request('statut_preparation') === 'prete' ? 'selected' : '' }}>Prête à envoyer</option>
                        <option value="ouverture_gmail" {{ request('statut_preparation') === 'ouverture_gmail' ? 'selected' : '' }}>Ouverte dans Gmail</option>
                        <option value="envoyee" {{ request('statut_preparation') === 'envoyee' ? 'selected' : '' }}>Déclarée envoyée</option>
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end gap-2 pt-1 border-t border-border/50">
                    <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-light transition-colors">
                        Appliquer les filtres
                    </button>
                    @if (request()->hasAny(['search', 'priorite', 'statut_preparation', 'date_debut', 'date_fin']))
                        <a href="{{ route('it.reports.demandes') }}" class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors" title="Réinitialiser">
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <x-ui.table
            :headers="['Référence', 'Matériel demandé', 'Quantité', 'Motif', 'Priorité', 'Date de création', 'Statut', 'Actions']"
            :isEmpty="$demandes->isEmpty()"
            emptyIcon="mail"
            emptyTitle="Aucune demande trouvée"
            emptyDescription="Aucune demande de matériel ne correspond aux filtres appliqués."
        >
            @foreach ($demandes as $d)
                <tr class="hover:bg-surface/50 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap font-mono text-xs font-bold text-primary">
                        {{ $d->reference }}
                    </td>
                    <td class="px-5 py-3 text-xs">
                        <div class="font-bold text-content">{{ $d->produit_designation ?? ($d->produit->nom ?? $d->produit->designation ?? '-') }}</div>
                        @if ($d->produit && $d->produit->reference)
                            <div class="text-[11px] text-content-secondary">Réf: {{ $d->produit->reference }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs font-bold text-content">
                        {{ $d->quantite }} unité(s)
                    </td>
                    <td class="px-5 py-3 text-xs text-content max-w-xs truncate" title="{{ $d->motif }}">
                        {{ $d->motif ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-xs whitespace-nowrap">
                        @if ($d->priorite === 'urgente')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Urgente
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-surface text-content-secondary border border-border">
                                Normale
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-content-secondary whitespace-nowrap">
                        {{ $d->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-3 text-xs whitespace-nowrap">
                        @if ($d->statut_preparation === 'envoyee')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Envoyée (déclaratif)
                            </span>
                        @elseif ($d->statut_preparation === 'ouverture_gmail')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-primary border border-blue-200">
                                Ouverte dans Gmail
                            </span>
                        @elseif ($d->statut_preparation === 'prete')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Prête à envoyer
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                Brouillon
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-right text-xs">
                        <a
                            href="{{ route('it.demandes.show', $d) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-border bg-white text-xs font-medium text-content hover:bg-surface transition-colors"
                        >
                            <x-ui.icon name="eye" class="w-3.5 h-3.5 text-content-secondary" />
                            Voir
                        </a>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $demandes->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>
    </div>

</x-layouts.dashboard>
