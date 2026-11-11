<x-layouts.dashboard title="Journal d'activités" :breadcrumbs="['Administration' => route('admin.dashboard'), 'Journal d\'activités' => null]">

    <div>
        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="activity" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Journal d'activités & Audit de sécurité</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-2xl">
                        Traçabilité chronologique inaltérable des créations, modifications, flux de stock et opérations administratives sensibles.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Form -->
        <div class="bg-white rounded-2xl border border-border p-5 mb-8 shadow-xs">
            <form method="GET" action="{{ route('admin.activities.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5">
                <!-- Search -->
                <div class="lg:col-span-2 relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher description, élément, auteur..."
                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <!-- Module -->
                <div>
                    <select name="module" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous les modules</option>
                        <option value="utilisateurs" {{ request('module') === 'utilisateurs' ? 'selected' : '' }}>Utilisateurs</option>
                        <option value="catalogue" {{ request('module') === 'catalogue' ? 'selected' : '' }}>Catalogue</option>
                        <option value="equipements" {{ request('module') === 'equipements' ? 'selected' : '' }}>Équipements</option>
                        <option value="stock" {{ request('module') === 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="mouvements" {{ request('module') === 'mouvements' ? 'selected' : '' }}>Mouvements</option>
                        <option value="demandes" {{ request('module') === 'demandes' ? 'selected' : '' }}>Demandes</option>
                        <option value="parametres" {{ request('module') === 'parametres' ? 'selected' : '' }}>Paramètres</option>
                    </select>
                </div>

                <!-- Action Type -->
                <div>
                    <select name="action_type" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous types d'action</option>
                        <option value="creation" {{ request('action_type') === 'creation' ? 'selected' : '' }}>Création</option>
                        <option value="modification" {{ request('action_type') === 'modification' ? 'selected' : '' }}>Modification</option>
                        <option value="archivage" {{ request('action_type') === 'archivage' ? 'selected' : '' }}>Archivage</option>
                        <option value="activation" {{ request('action_type') === 'activation' ? 'selected' : '' }}>Activation</option>
                        <option value="desactivation" {{ request('action_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                        <option value="entree" {{ request('action_type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie" {{ request('action_type') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                        <option value="transfert" {{ request('action_type') === 'transfert' ? 'selected' : '' }}>Transfert</option>
                        <option value="correction" {{ request('action_type') === 'correction' ? 'selected' : '' }}>Correction</option>
                        <option value="ouverture_gmail" {{ request('action_type') === 'ouverture_gmail' ? 'selected' : '' }}>Ouverture Gmail</option>
                    </select>
                </div>

                <!-- User -->
                <div>
                    <select name="user_id" class="w-full px-3 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Tous les auteurs</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-light transition-colors">
                        Filtrer
                    </button>
                    @if (request()->hasAny(['search', 'module', 'action_type', 'user_id', 'date_debut', 'date_fin']))
                        <a href="{{ route('admin.activities.index') }}" class="p-2.5 border border-border hover:bg-surface text-content-secondary rounded-xl transition-colors" title="Réinitialiser">
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <x-ui.table
            :headers="['Date & Heure', 'Auteur', 'Module', 'Type d\'action', 'Élément concerné', 'Description de l\'opération', 'Détails']"
            :isEmpty="$logs->isEmpty()"
            emptyIcon="activity"
            emptyTitle="Aucun événement enregistré"
            emptyDescription="Les événements et opérations auditées apparaîtront ici."
        >
            @foreach ($logs as $log)
                <tr class="hover:bg-surface/50 transition-colors">
                    <!-- Date -->
                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-content font-medium">
                        <div>{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-[11px] text-content-secondary font-mono">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>

                    <!-- Auteur -->
                    <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                        <div class="font-bold text-content">{{ $log->user_name ?? ($log->user->name ?? 'Système') }}</div>
                        @if ($log->ip_address)
                            <div class="text-[10px] text-content-secondary font-mono">{{ $log->ip_address }}</div>
                        @endif
                    </td>

                    <!-- Module -->
                    <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-surface border border-border text-content-secondary uppercase">
                            {{ $log->module }}
                        </span>
                    </td>

                    <!-- Action -->
                    <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $log->action_type === 'creation' || $log->action_type === 'entree' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($log->action_type === 'desactivation' || $log->action_type === 'archivage' || $log->action_type === 'sortie' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-primary/10 text-primary border border-primary/20') }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                        </span>
                    </td>

                    <!-- Élément concerné -->
                    <td class="px-5 py-3.5 text-xs text-content font-medium max-w-[180px] truncate" title="{{ $log->entity_name }}">
                        {{ $log->entity_name ?? '-' }}
                    </td>

                    <!-- Description -->
                    <td class="px-5 py-3.5 text-xs text-content max-w-xs truncate" title="{{ $log->description }}">
                        {{ $log->description }}
                    </td>

                    <!-- Détails -->
                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs">
                        <a
                            href="{{ route('admin.activities.show', $log) }}"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-border bg-white text-xs font-semibold text-content hover:bg-surface hover:text-primary transition-colors"
                        >
                            <x-ui.icon name="eye" class="w-3.5 h-3.5 text-content-secondary" />
                            Détails
                        </a>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>
                <div class="w-full">
                    {{ $logs->links() }}
                </div>
            </x-slot:footer>
        </x-ui.table>
    </div>

</x-layouts.dashboard>
