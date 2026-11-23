<x-layouts.dashboard title="Demandes de matériel" :breadcrumbs="['Demandes de matériel' => null]">

    <div class="space-y-6">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="p-4 bg-white border border-emerald-200 rounded-2xl flex items-center gap-3 text-sm text-content shadow-xs">
                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="check-circle" class="w-4 h-4" />
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-white border border-rose-200 rounded-2xl flex items-center gap-3 text-sm text-content shadow-xs">
                <span class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="alert-triangle" class="w-4 h-4" />
                </span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header Banner -->
        <div class="p-5 sm:p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="mail" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-bold text-content tracking-tight">Demandes de matériel</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-0.5">
                        Préparez vos demandes de matériel.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('it.demandes.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors w-full sm:w-auto"
            >
                <x-ui.icon name="plus" class="w-4 h-4" />
                Nouvelle demande
            </a>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total -->
            <div class="bg-white rounded-2xl border border-border p-5 flex items-center gap-4 shadow-2xs">
                <div class="w-11 h-11 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="file-text" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <span class="text-[11px] font-semibold text-content-secondary uppercase tracking-wider block">Total des demandes</span>
                    <span class="text-2xl font-bold text-content mt-0.5 block leading-none">{{ $totalDemandes }}</span>
                </div>
            </div>

            <!-- Brouillons -->
            <div class="bg-white rounded-2xl border border-border p-5 flex items-center gap-4 shadow-2xs">
                <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="edit-3" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <span class="text-[11px] font-semibold text-content-secondary uppercase tracking-wider block">Brouillons</span>
                    <span class="text-2xl font-bold text-content mt-0.5 block leading-none">{{ $brouillonsCount }}</span>
                </div>
            </div>

            <!-- À traiter -->
            <div class="bg-white rounded-2xl border border-border p-5 flex items-center gap-4 shadow-2xs">
                <div class="w-11 h-11 rounded-full bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="clock" class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <span class="text-[11px] font-semibold text-content-secondary uppercase tracking-wider block">Demandes à traiter</span>
                    <span class="text-2xl font-bold text-content mt-0.5 block leading-none">{{ $demandesATraiterCount }}</span>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-border p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('it.demandes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="sm:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher référence, matériel, motif..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                </div>

                <!-- Priorité -->
                <select
                    name="priorite"
                    class="w-full px-3 py-2.5 bg-white border border-border rounded-xl text-xs text-content focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
                    <option value="">Toutes les priorités</option>
                    <option value="normale" {{ request('priorite') === 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="importante" {{ request('priorite') === 'importante' ? 'selected' : '' }}>Importante</option>
                    <option value="urgente" {{ request('priorite') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                </select>

                <!-- Statut -->
                <select
                    name="statut_preparation"
                    class="w-full px-3 py-2.5 bg-white border border-border rounded-xl text-xs text-content focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut_preparation') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="prete" {{ request('statut_preparation') === 'prete' ? 'selected' : '' }}>Préparée</option>
                    <option value="ouverture_gmail" {{ request('statut_preparation') === 'ouverture_gmail' ? 'selected' : '' }}>Ouverte dans Gmail</option>
                    <option value="envoyee" {{ request('statut_preparation') === 'envoyee' ? 'selected' : '' }}>Déclarée envoyée</option>
                </select>

                <!-- Boutons -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 sm:flex-none px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl text-xs font-semibold transition-colors"
                    >
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'priorite', 'statut_preparation', 'show_archived']))
                        <a
                            href="{{ route('it.demandes.index') }}"
                            class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Demandes Table -->
        <x-ui.table
            :headers="['Référence', 'Matériel', 'Quantité demandée', 'Priorité', 'Statut', 'Date', 'Actions']"
            :isEmpty="$demandes->isEmpty()"
            emptyIcon="mail"
            emptyTitle="Aucune demande trouvée"
            emptyDescription="Vous n'avez préparé aucune demande de matériel pour le moment."
        >
            @foreach ($demandes as $demande)
                <tr class="hover:bg-surface/60 transition-colors">
                    <!-- Référence -->
                    <td class="px-5 py-4 whitespace-nowrap">
                        <a href="{{ route('it.demandes.show', $demande) }}" class="font-mono text-xs font-bold text-primary hover:underline">
                            {{ $demande->reference }}
                        </a>
                    </td>

                    <!-- Matériel -->
                    <td class="px-5 py-4 min-w-[180px]">
                        <div class="text-xs font-semibold text-content">
                            {{ $demande->produit_designation ?? ($demande->produit->nom ?? 'N/A') }}
                        </div>
                        <div class="text-[11px] text-content-secondary mt-0.5">
                            Réf: {{ $demande->produit_reference ?? $demande->produit->reference }}
                        </div>
                    </td>

                    <!-- Quantité demandée -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs font-semibold text-content">
                        {{ $demande->quantite }} unité(s)
                        @if ($demande->prix_estime)
                            <div class="text-[11px] text-content-secondary font-normal mt-0.5">
                                ~ {{ number_format($demande->prix_estime * $demande->quantite, 2, ',', ' ') }} €
                            </div>
                        @endif
                    </td>

                    <!-- Priorité -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        @if ($demande->priorite === 'urgente')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                <x-ui.icon name="alert-triangle" class="w-3 h-3" />
                                Urgente
                            </span>
                        @elseif ($demande->priorite === 'importante')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                <x-ui.icon name="alert-circle" class="w-3 h-3" />
                                Importante
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-surface text-content-secondary">
                                Normale
                            </span>
                        @endif
                    </td>

                    <!-- Statut -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                        @if ($demande->statut_preparation === 'envoyee')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Envoyée (déclaratif)
                            </span>
                        @elseif ($demande->statut_preparation === 'ouverture_gmail')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary/10 text-primary">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                Ouverte dans Gmail
                            </span>
                        @elseif ($demande->statut_preparation === 'prete')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-accent/10 text-accent">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                Préparée
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Brouillon
                            </span>
                        @endif
                    </td>

                    <!-- Date -->
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-content-secondary">
                        {{ $demande->created_at->format('d/m/Y') }}
                    </td>

                    <!-- Actions -->
                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                        <div class="flex items-center justify-end gap-1">
                            <!-- Voir détails -->
                            <a
                                href="{{ route('it.demandes.show', $demande) }}"
                                class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-primary hover:bg-primary/10 rounded-full transition-colors"
                                title="Voir les détails"
                            >
                                <x-ui.icon name="eye" class="w-4 h-4" />
                            </a>

                            <!-- Modifier si brouillon -->
                            @if ($demande->statut_preparation === 'brouillon')
                                <a
                                    href="{{ route('it.demandes.edit', $demande) }}"
                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-accent hover:bg-accent/10 rounded-full transition-colors"
                                    title="Modifier le brouillon"
                                >
                                    <x-ui.icon name="edit" class="w-4 h-4" />
                                </a>
                            @endif

                            <!-- Ouvrir dans Gmail -->
                            @if ($demande->email_destinataire)
                                <a
                                    href="{{ $demande->gmail_compose_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-primary hover:bg-primary/10 rounded-full transition-colors"
                                    title="Ouvrir dans Gmail"
                                >
                                    <x-ui.icon name="mail" class="w-4 h-4" />
                                </a>
                            @endif

                            <!-- Déclarer envoyée -->
                            @if (in_array($demande->statut_preparation, ['prete', 'ouverture_gmail']))
                                <form method="POST" action="{{ route('it.demandes.mark-sent', $demande) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-emerald-600 hover:bg-emerald-50 rounded-full transition-colors"
                                        title="Marquer comme envoyée (suivi déclaratif)"
                                        onclick="return confirm('Confirmez-vous avoir envoyé cet e-mail depuis votre messagerie Gmail ?')"
                                    >
                                        <x-ui.icon name="check-circle" class="w-4 h-4" />
                                    </button>
                                </form>
                            @endif

                            <!-- Dupliquer -->
                            <form method="POST" action="{{ route('it.demandes.duplicate', $demande) }}" class="inline">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-content hover:bg-surface rounded-full transition-colors"
                                    title="Dupliquer la demande"
                                >
                                    <x-ui.icon name="copy" class="w-4 h-4" />
                                </button>
                            </form>


                            <!-- Archiver -->
                            <form method="POST" action="{{ route('it.demandes.archive', $demande) }}" class="inline" onsubmit="return confirm('Voulez-vous vraiment archiver cette demande ?')">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-rose-600 hover:bg-rose-50 rounded-full transition-colors"
                                    title="Archiver la demande"
                                >
                                    <x-ui.icon name="archive" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
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