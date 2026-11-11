<x-layouts.dashboard title="Tableau de bord" :breadcrumbs="['Tableau de bord' => null]">

    <!-- 1. En-tête compact et moderne -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-extrabold text-content tracking-tight">Tableau de bord</h1>
                <x-ui.badge variant="it" size="sm" :dot="true">Gestionnaire de stock</x-ui.badge>
            </div>
            <p class="text-xs text-content-secondary mt-1">
                Consultez votre stock et les alertes.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a
                href="{{ route('it.products.create') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
            >
                <x-ui.icon name="plus" class="w-4 h-4" />
                Ajouter un matériel
            </a>
            <a
                href="{{ route('it.stocks.index') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-border bg-white hover:bg-surface text-content text-xs font-semibold transition-colors"
            >
                <x-ui.icon name="boxes" class="w-4 h-4 text-primary" />
                Gestion du stock
            </a>
        </div>
    </div>

    <!-- 2. Section des Alertes de Stock (directement sous le titre) -->
    <div class="mb-8 space-y-3">
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-content">
            <x-ui.icon name="bell" class="w-4 h-4 text-accent" />
            <span>Alertes de stock</span>
            @if (!empty($alertesStock) && count($alertesStock) > 0)
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-accent/10 text-accent">
                    {{ count($alertesStock) }}
                </span>
            @endif
        </div>

        @if (empty($alertesStock) || count($alertesStock) === 0)
            <div class="p-4 bg-white rounded-xl border border-border text-xs text-content-secondary flex items-center gap-2.5">
                <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0" />
                <span>Aucun stock faible ou épuisé.</span>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($alertesStock as $alerte)
                    @php
                        $prod = $alerte['produit'];
                        $isRupture = ($alerte['type'] === 'rupture');
                    @endphp
                    <div class="p-4 rounded-xl border flex items-center justify-between gap-4 transition-all {{ $isRupture ? 'bg-red-50/70 border-red-200 text-red-900' : 'bg-amber-50/70 border-amber-200 text-amber-900' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $isRupture ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                                <x-ui.icon :name="$isRupture ? 'package-x' : 'alert-triangle'" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold truncate text-content">
                                        {{ $prod->nom }}
                                    </h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $isRupture ? 'bg-red-200/80 text-red-800' : 'bg-amber-200/80 text-amber-800' }}">
                                        {{ $isRupture ? 'Épuisé' : 'Stock faible' }}
                                    </span>
                                </div>
                                <p class="text-xs mt-0.5 {{ $isRupture ? 'text-red-700' : 'text-amber-700' }}">
                                    @if ($isRupture)
                                        Ce produit est épuisé.
                                    @else
                                        Il reste 1 unité en stock.
                                    @endif
                                    @if ($prod->reference)
                                        <span class="text-content-secondary/60">&bull;</span>
                                        <span class="font-mono text-[11px] text-content-secondary">Réf : {{ $prod->reference }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Bouton Demander qui ouvre Gmail prérempli -->
                        <a
                            href="{{ $alerte['gmail_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white shadow-xs transition-colors flex-shrink-0 {{ $isRupture ? 'bg-red-600 hover:bg-red-700' : 'bg-accent hover:bg-accent-light' }}"
                            title="Ouvrir Gmail avec le message prérempli"
                        >
                            <x-ui.icon name="mail" class="w-4 h-4" />
                            Demander
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 3. Exactement quatre cartes statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Carte 1 — Produits enregistrés -->
        <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                    Produits enregistrés
                </p>
                <p class="text-2xl font-extrabold text-content">
                    {{ $totalProduits }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <x-ui.icon name="package" class="w-6 h-6" />
            </div>
        </div>

        <!-- Carte 2 — Unités en stock -->
        <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                    Unités en stock
                </p>
                <p class="text-2xl font-extrabold text-primary">
                    {{ number_format($totalUnitesStock) }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary-light flex items-center justify-center flex-shrink-0">
                <x-ui.icon name="boxes" class="w-6 h-6" />
            </div>
        </div>

        <!-- Carte 3 — Stock faible -->
        <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                    Stock faible
                </p>
                <p class="text-2xl font-extrabold text-accent">
                    {{ $stockFaibleCount }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-accent flex items-center justify-center flex-shrink-0">
                <x-ui.icon name="alert-triangle" class="w-6 h-6" />
            </div>
        </div>

        <!-- Carte 4 — Produits épuisés -->
        <div class="bg-white rounded-xl border border-border p-5 shadow-xs flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-content-secondary uppercase tracking-wider mb-1">
                    Produits épuisés
                </p>
                <p class="text-2xl font-extrabold {{ $produitsEpuisesCount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $produitsEpuisesCount }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $produitsEpuisesCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center flex-shrink-0">
                <x-ui.icon name="package-x" class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- 4. Actions rapides -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold text-content uppercase tracking-wider">
                Actions rapides
            </h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Ajouter un matériel -->
            <a
                href="{{ route('it.products.create') }}"
                class="group p-4 bg-white hover:bg-surface border border-border hover:border-primary/50 rounded-xl shadow-xs transition-all flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    <x-ui.icon name="package-plus" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content group-hover:text-primary transition-colors">
                        Ajouter un matériel
                    </h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Nouveau produit</p>
                </div>
            </a>

            <!-- Entrée de stock -->
            <a
                href="{{ route('it.stocks.entree') }}"
                class="group p-4 bg-white hover:bg-surface border border-border hover:border-emerald-400 rounded-xl shadow-xs transition-all flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    <x-ui.icon name="plus" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content group-hover:text-emerald-700 transition-colors">
                        Entrée de stock
                    </h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Ajouter du stock</p>
                </div>
            </a>

            <!-- Sortie de stock -->
            <a
                href="{{ route('it.stocks.sortie') }}"
                class="group p-4 bg-white hover:bg-surface border border-border hover:border-rose-400 rounded-xl shadow-xs transition-all flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    <x-ui.icon name="minus" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content group-hover:text-rose-700 transition-colors">
                        Sortie de stock
                    </h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Retirer du stock</p>
                </div>
            </a>

            <!-- Nouvelle demande -->
            <a
                href="{{ route('it.demandes.create') }}"
                class="group p-4 bg-white hover:bg-surface border border-border hover:border-accent rounded-xl shadow-xs transition-all flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent group-hover:bg-accent group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    <x-ui.icon name="mail" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content group-hover:text-accent transition-colors">
                        Nouvelle demande
                    </h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Demander du matériel</p>
                </div>
            </a>

            <!-- Voir l'historique -->
            <a
                href="{{ route('it.mouvements.index') }}"
                class="group p-4 bg-white hover:bg-surface border border-border hover:border-blue-400 rounded-xl shadow-xs transition-all flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                    <x-ui.icon name="history" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-content group-hover:text-blue-700 transition-colors">
                        Voir l'historique
                    </h4>
                    <p class="text-[11px] text-content-secondary mt-0.5">Journal des mouvements</p>
                </div>
            </a>
        </div>
    </div>

    <!-- 5. Tableau compact des 5 derniers mouvements -->
    <div class="bg-white rounded-2xl border border-border shadow-xs p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                    Derniers mouvements
                </h3>
                <p class="text-xs text-content-secondary mt-0.5">
                    Historique des 5 dernières opérations enregistrées
                </p>
            </div>
            <a href="{{ route('it.mouvements.index') }}" class="text-xs font-semibold text-primary hover:underline inline-flex items-center gap-1">
                Voir l'historique &rarr;
            </a>
        </div>

        @if (empty($recentMovements) || $recentMovements->isEmpty())
            <div class="py-8 text-center text-xs text-content-secondary">
                <x-ui.icon name="history" class="w-8 h-8 mx-auto text-content-secondary/40 mb-2" />
                Aucun mouvement récent.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-border text-[11px] font-bold text-content-secondary uppercase">
                            <th class="pb-3 px-3">Matériel</th>
                            <th class="pb-3 px-3">Mouvement</th>
                            <th class="pb-3 px-3 text-right">Quantité</th>
                            <th class="pb-3 px-3">Emplacement</th>
                            <th class="pb-3 px-3">Date</th>
                            <th class="pb-3 px-3">Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($recentMovements as $m)
                            <tr class="hover:bg-surface/50 transition-colors">
                                <td class="py-3 px-3 font-semibold text-content">
                                    {{ $m->produit->nom ?? '-' }}
                                    @if ($m->produit->reference)
                                        <span class="block text-[10px] text-content-secondary font-mono">
                                            {{ $m->produit->reference }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @if ($m->type === 'entree')
                                        <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Entrée
                                        </span>
                                    @elseif ($m->type === 'sortie')
                                        <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] bg-rose-50 text-rose-700 border border-rose-200">
                                            Sortie
                                        </span>
                                    @elseif ($m->type === 'transfert')
                                        <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] bg-blue-50 text-blue-700 border border-blue-200">
                                            Transfert
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] bg-amber-50 text-amber-700 border border-amber-200">
                                            Correction
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-content">
                                    @if ($m->type === 'entree')
                                        <span class="text-emerald-600">+{{ $m->quantite }}</span>
                                    @elseif ($m->type === 'sortie')
                                        <span class="text-rose-600">-{{ $m->quantite }}</span>
                                    @else
                                        {{ $m->quantite }}
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-content-secondary text-[11px]">
                                    @if ($m->type === 'entree')
                                        {{ $m->emplacementDestination->nom ?? 'Stock' }}
                                    @elseif ($m->type === 'sortie')
                                        {{ $m->emplacementSource->nom ?? 'Stock' }}
                                    @elseif ($m->type === 'transfert')
                                        {{ $m->emplacementSource->nom ?? '-' }} &rarr; {{ $m->emplacementDestination->nom ?? '-' }}
                                    @else
                                        {{ $m->emplacementDestination->nom ?? $m->emplacementSource->nom ?? '-' }}
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-content-secondary whitespace-nowrap">
                                    {{ $m->date_mouvement ? $m->date_mouvement->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-content whitespace-nowrap">
                                    {{ $m->user->name ?? 'Système' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.dashboard>
