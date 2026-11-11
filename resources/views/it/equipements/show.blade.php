<x-layouts.dashboard :title="'Équipement ' . $equipement->code_inventaire" :breadcrumbs="['Équipements' => route('it.equipements.index'), $equipement->code_inventaire => null]">

    <div
        x-data="{
            deleteModalOpen: false
        }"
        @keydown.escape.window="deleteModalOpen = false"
    >

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

        <!-- Top Header & Action Bar -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a
                    href="{{ route('it.equipements.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-content-secondary hover:text-primary transition-colors mb-2"
                >
                    <x-ui.icon name="chevron-left" class="w-4 h-4" />
                    Retour aux équipements
                </a>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-content tracking-tight">
                        Équipement <code class="font-mono text-primary font-extrabold">{{ $equipement->code_inventaire }}</code>
                    </h1>

                    <!-- État physique badge -->
                    @if ($equipement->isBonEtat())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $equipement->etat_physique_label }}
                        </span>
                    @elseif ($equipement->etat_physique === \App\Models\Equipement::ETAT_A_VERIFIER)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $equipement->etat_physique_label }}
                        </span>
                    @elseif ($equipement->isEnPanne())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            {{ $equipement->etat_physique_label }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                            {{ $equipement->etat_physique_label }}
                        </span>
                    @endif

                    <!-- Statut d'utilisation badge -->
                    @if ($equipement->isDisponible())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $equipement->statut_utilisation_label }}
                        </span>
                    @elseif ($equipement->isEnUtilisation())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-primary-light border border-blue-200">
                            {{ $equipement->statut_utilisation_label }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $equipement->statut_utilisation_label }}
                        </span>
                    @endif

                    <!-- Statut archivage badge -->
                    @if ($equipement->isArchive())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            Archivé
                        </span>
                    @endif
                </div>

                <p class="text-xs text-content-secondary mt-1">
                    Modèle rattaché : <strong class="text-content font-semibold">{{ $equipement->produit->nom }}</strong>
                    @if ($equipement->numero_serie)
                        &bull; S/N : <code class="font-mono font-semibold text-content px-1.5 py-0.5 rounded bg-surface border border-border">{{ $equipement->numero_serie }}</code>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center flex-wrap">
                <!-- Edit button -->
                <a
                    href="{{ route('it.equipements.edit', $equipement) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="pencil" class="w-4 h-4" />
                    Modifier
                </a>

                <!-- Toggle status button -->
                <form method="POST" action="{{ route('it.equipements.toggle-status', $equipement) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                        title="{{ $equipement->isActif() ? 'Archiver cet équipement' : 'Réactiver cet équipement' }}"
                    >
                        <x-ui.icon name="archive" class="w-4 h-4 text-content-secondary" />
                        {{ $equipement->isActif() ? 'Archiver' : 'Réactiver' }}
                    </button>
                </form>

                <!-- Delete button -->
                <button
                    type="button"
                    @click="deleteModalOpen = true"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-xs font-semibold text-red-700 transition-colors"
                >
                    <x-ui.icon name="trash-2" class="w-4 h-4" />
                    Supprimer
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN: PRODUCT MODEL & SITUATION -->
            <div class="space-y-6">

                <!-- Product Model Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs text-center">
                    <div class="w-full h-44 rounded-xl overflow-hidden bg-surface border border-border flex items-center justify-center relative mb-4">
                        @if ($equipement->produit->photo_url)
                            <img
                                src="{{ $equipement->produit->photo_url }}"
                                alt="{{ $equipement->produit->nom }}"
                                class="w-full h-full object-cover"
                            />
                        @else
                            <div class="flex flex-col items-center justify-center text-content-secondary">
                                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-2">
                                    <x-ui.icon :name="$equipement->produit->categorie->icon_name ?? 'laptop'" class="w-7 h-7" />
                                </div>
                                <span class="text-[11px] text-content-secondary/80">Visuel catalogue</span>
                            </div>
                        @endif
                    </div>

                    <h2 class="text-base font-bold text-content">{{ $equipement->produit->nom }}</h2>
                    <p class="text-xs text-content-secondary mt-0.5">
                        {{ $equipement->produit->marque ?: 'Constructeur non précisé' }}
                        @if ($equipement->produit->modele)
                            &bull; {{ $equipement->produit->modele }}
                        @endif
                    </p>

                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between text-xs">
                        <span class="text-content-secondary">Catégorie :</span>
                        <span class="font-semibold text-content">{{ $equipement->produit->categorie->nom ?? 'Non classé' }}</span>
                    </div>

                    <div class="mt-2 flex items-center justify-between text-xs">
                        <span class="text-content-secondary">SKU catalogue :</span>
                        <code class="font-mono text-content font-medium">{{ $equipement->produit->reference }}</code>
                    </div>

                    <div class="mt-4">
                        <a
                            href="{{ route('it.products.show', $equipement->produit) }}"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline"
                        >
                            <x-ui.icon name="external-link" class="w-3.5 h-3.5" />
                            Consulter la fiche modèle au catalogue
                        </a>
                    </div>
                </div>

                <!-- Attributes Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <h3 class="text-xs font-bold text-content uppercase tracking-wider mb-4 pb-3 border-b border-border">
                        Fiche technique unitaire
                    </h3>

                    <dl class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Code inventaire</dt>
                            <dd class="font-mono font-bold text-content">{{ $equipement->code_inventaire }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Numéro de série</dt>
                            <dd class="font-mono font-medium text-content">{{ $equipement->numero_serie ?: '—' }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Emplacement</dt>
                            <dd class="font-semibold text-content">
                                @if ($equipement->emplacement)
                                    <span class="inline-flex items-center gap-1 text-content">
                                        <x-ui.icon name="map-pin" class="w-3.5 h-3.5 text-accent" />
                                        {{ $equipement->emplacement->nom }}
                                    </span>
                                @else
                                    <span class="text-content-secondary italic">Non assigné</span>
                                @endif
                            </dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Date d'entrée</dt>
                            <dd class="font-medium text-content">{{ $equipement->date_entree ? $equipement->date_entree->format('d/m/Y') : '—' }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">État physique</dt>
                            <dd class="font-semibold text-content">{{ $equipement->etat_physique_label }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Statut d'utilisation</dt>
                            <dd class="font-semibold text-content">{{ $equipement->statut_utilisation_label }}</dd>
                        </div>

                        <div class="pt-3 border-t border-border flex items-center justify-between text-[11px]">
                            <dt class="text-content-secondary">Créé par</dt>
                            <dd class="text-content font-medium">{{ $equipement->createur->name ?? 'Système' }}</dd>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <dt class="text-content-secondary">Date de création</dt>
                            <dd class="text-content-secondary">{{ $equipement->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        @if ($equipement->modificateur)
                            <div class="flex items-center justify-between text-[11px]">
                                <dt class="text-content-secondary">Dernière modif. par</dt>
                                <dd class="text-content font-medium">{{ $equipement->modificateur->name }}</dd>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-[11px]">
                            <dt class="text-content-secondary">Dernière mise à jour</dt>
                            <dd class="text-content-secondary">{{ $equipement->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>

            </div>

            <!-- RIGHT COLUMN: OBSERVATIONS, TRAÇABILITÉ (AUDIT TRAIL) & FUTURE MOVEMENTS -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Observations Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-border">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <x-ui.icon name="file-text" class="w-4 h-4" />
                        </div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                            Observations & Notes techniques
                        </h3>
                    </div>

                    @if ($equipement->observations)
                        <div class="text-xs text-content leading-relaxed whitespace-pre-line">
                            {{ $equipement->observations }}
                        </div>
                    @else
                        <p class="text-xs text-content-secondary italic">Aucune observation spécifique n'a été enregistrée pour cet équipement.</p>
                    @endif
                </div>

                <!-- Activity Log / Audit Trail -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <x-ui.icon name="history" class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                                    Journal des modifications & Traçabilité
                                </h3>
                                <p class="text-[11px] text-content-secondary">Historique des changements intervenus sur la fiche</p>
                            </div>
                        </div>

                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-surface text-content-secondary border border-border">
                            {{ $equipement->activites->count() }} événement(s)
                        </span>
                    </div>

                    @if ($equipement->activites->isEmpty())
                        <p class="text-xs text-content-secondary italic text-center py-4">Aucune activité enregistrée.</p>
                    @else
                        <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-border">
                            @foreach ($equipement->activites as $act)
                                <div class="relative">
                                    <!-- Timeline Dot -->
                                    <div class="absolute -left-6 top-1.5 w-3 h-3 rounded-full border-2 border-white bg-primary shadow-xs"></div>

                                    <div class="p-4 rounded-xl bg-surface/60 border border-border">
                                        <div class="flex items-center justify-between gap-3 mb-1.5 flex-wrap">
                                            <div class="flex items-center gap-2">
                                                @if ($act->action === 'creation')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
                                                        Création
                                                    </span>
                                                @elseif ($act->action === 'modification')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-primary-light border border-blue-200 uppercase tracking-wide">
                                                        Modification
                                                    </span>
                                                @elseif ($act->action === 'archivage')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wide">
                                                        Archivage
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
                                                        Réactivation
                                                    </span>
                                                @endif

                                                <span class="text-xs font-bold text-content">{{ $act->description }}</span>
                                            </div>

                                            <div class="flex items-center gap-2 text-[11px] text-content-secondary">
                                                <span>{{ $act->user->name ?? 'Système' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $act->created_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                        </div>

                                        <!-- Difference details if modified -->
                                        @if ($act->anciennes_valeurs && $act->nouvelles_valeurs)
                                            <div class="mt-3 pt-3 border-t border-border grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                                                <div>
                                                    <span class="block text-[10px] font-bold uppercase text-content-secondary mb-1">Avant modification :</span>
                                                    <ul class="space-y-0.5 text-content-secondary">
                                                        @foreach ($act->anciennes_valeurs as $field => $val)
                                                            @if (isset($act->nouvelles_valeurs[$field]) && $act->nouvelles_valeurs[$field] !== $val)
                                                                <li>
                                                                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $field) }} :</span>
                                                                    <span class="line-through text-red-600">{{ $val ?: 'Vide' }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>

                                                <div>
                                                    <span class="block text-[10px] font-bold uppercase text-content-secondary mb-1">Nouvelle valeur :</span>
                                                    <ul class="space-y-0.5 text-content">
                                                        @foreach ($act->nouvelles_valeurs as $field => $val)
                                                            @if (isset($act->anciennes_valeurs[$field]) && $act->anciennes_valeurs[$field] !== $val)
                                                                <li>
                                                                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $field) }} :</span>
                                                                    <span class="font-bold text-emerald-600">{{ $val ?: 'Vide' }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Future Mouvements & Affectations Placeholder -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center">
                                <x-ui.icon name="activity" class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                                    Historique des mouvements & Affectations
                                </h3>
                                <p class="text-[11px] text-content-secondary">Transferts d'emplacements, entrées, sorties et prêts</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-accent border border-amber-200">
                            Prochaine étape &mdash; Stocks & Mouvements
                        </span>
                    </div>

                    <div class="p-5 rounded-xl bg-surface/70 border border-border text-center">
                        <div class="w-12 h-12 rounded-xl bg-white mx-auto flex items-center justify-center text-accent mb-3 shadow-2xs border border-border">
                            <x-ui.icon name="boxes" class="w-6 h-6" />
                        </div>

                        <h4 class="text-sm font-bold text-content">Prêt pour l'intégration des flux de stock</h4>
                        <p class="text-xs text-content-secondary max-w-lg mx-auto mt-1 leading-relaxed">
                            Cet équipement (<code class="font-mono font-semibold">{{ $equipement->code_inventaire }}</code>) est désormais parfaitement répertorié. Lors du développement du module Mouvements, tous les bons de transfert, changements d'emplacements et affectations nominatives s'agrégeront automatiquement ici.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        <!-- Delete Confirmation Modal -->
        <div
            x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>

            <div class="relative w-full max-w-md mx-auto my-12 bg-white rounded-2xl border border-border shadow-2xl p-6 overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="trash-2" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-content">Supprimer l'équipement</h3>
                        <p class="text-xs text-content-secondary mt-1">
                            Êtes-vous sûr de vouloir supprimer définitivement l'équipement
                            <strong class="text-content">« {{ $equipement->code_inventaire }} »</strong> ?
                        </p>
                        <p class="text-[11px] text-content-secondary mt-2">
                            Attention : si cet équipement possède un historique de modifications, la suppression sera refusée par mesure de traçabilité. Préférez plutôt l'archivage.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="deleteModalOpen = false"
                        class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        Annuler
                    </button>
                    <form action="{{ route('it.equipements.destroy', $equipement) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                        >
                            Confirmer la suppression
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-layouts.dashboard>
