<x-layouts.dashboard title="Détail du mouvement" :breadcrumbs="['Gestion IT' => null, 'Mouvements' => route('it.mouvements.index'), 'Fiche '.$mouvement->reference => null]">

    <div class="max-w-4xl mx-auto">
        <!-- Header / Back -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="file-text" class="w-6 h-6" />
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-bold text-content tracking-tight">Bon de mouvement</h2>
                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-surface border border-border text-primary">
                            {{ $mouvement->reference }}
                        </span>
                    </div>
                    <p class="text-xs text-content-secondary mt-1">
                        Enregistré le {{ $mouvement->created_at->format('d/m/Y à H:i') }} par {{ $mouvement->user->name ?? 'Système' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a
                    href="{{ route('it.mouvements.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                    Retour à l'historique
                </a>
            </div>
        </div>

        <!-- Voucher Details Card -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden mb-8">
            <!-- Voucher Header -->
            <div class="p-6 sm:p-8 bg-surface/50 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Type d'opération</span>
                    <div class="mt-1">
                        @if ($mouvement->type === 'entree')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-ui.icon name="arrow-down-left" class="w-4 h-4 text-emerald-600" />
                                Entrée de stock (Approvisionnement)
                            </span>
                        @elseif ($mouvement->type === 'sortie')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                <x-ui.icon name="arrow-up-right" class="w-4 h-4" />
                                Sortie de stock (Distribution)
                            </span>
                        @elseif ($mouvement->type === 'transfert')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <x-ui.icon name="arrow-right-left" class="w-4 h-4" />
                                Transfert inter-emplacements
                            </span>
                        @elseif ($mouvement->type === 'correction')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-accent/10 text-accent border border-accent/20">
                                <x-ui.icon name="wrench" class="w-4 h-4" />
                                Régularisation d'inventaire
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Date du mouvement</span>
                    <span class="text-sm font-bold text-content mt-1 block">
                        {{ $mouvement->date_mouvement ? $mouvement->date_mouvement->format('d/m/Y H:i') : '-' }}
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <!-- Grid: Article & Quantité -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-5 rounded-xl border border-border bg-white">
                        <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block mb-2">Matériel concerné</span>
                        <div class="text-base font-bold text-content">
                            {{ $mouvement->produit->designation ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-content-secondary mt-1">
                            Référence catalogue : <strong class="text-content font-mono">{{ $mouvement->produit->reference ?? '-' }}</strong>
                        </div>
                        @if($mouvement->produit && $mouvement->produit->category)
                            <div class="text-xs text-content-secondary mt-0.5">
                                Catégorie : {{ $mouvement->produit->category->nom }}
                            </div>
                        @endif

                        @if($mouvement->equipement)
                            <div class="mt-4 pt-4 border-t border-border">
                                <span class="text-[11px] font-bold text-accent uppercase tracking-wider block mb-1">Unité physique sérialisée</span>
                                <div class="text-xs font-mono font-bold text-content">
                                    Numéro de série : {{ $mouvement->equipement->numero_serie }}
                                </div>
                                @if($mouvement->equipement->code_inventaire)
                                    <div class="text-xs text-content-secondary mt-0.5 font-mono">
                                        Code inventaire : {{ $mouvement->equipement->code_inventaire }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="p-5 rounded-xl border border-border bg-white flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block mb-2">Quantité mouvementée</span>
                            <div class="text-3xl font-extrabold text-content">
                                {{ $mouvement->quantite }} <span class="text-sm font-normal text-content-secondary">unité(s)</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-border">
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Trajet d'emplacement</span>
                            <div class="text-xs text-content flex items-center gap-2">
                                <span class="font-semibold">{{ $mouvement->emplacementSource->nom ?? 'Extérieur / Stock initial' }}</span>
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 text-content-secondary" />
                                <span class="font-semibold">{{ $mouvement->emplacementDestination->nom ?? 'Consommé / Sorti' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid: Traçabilité & Justification -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border">
                    <div>
                        <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block mb-1">Motif de l'opération</span>
                        <div class="text-xs text-content font-medium bg-surface p-3.5 rounded-xl border border-border">
                            {{ $mouvement->motif ?? 'Aucun motif renseigné' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block mb-1">Bénéficiaire / Destinataire</span>
                        <div class="text-xs text-content font-medium bg-surface p-3.5 rounded-xl border border-border">
                            {{ $mouvement->beneficiaire ?? 'Non applicable' }}
                        </div>
                    </div>

                    @if($mouvement->commentaire)
                        <div class="md:col-span-2">
                            <span class="text-xs font-bold text-content-secondary uppercase tracking-wider block mb-1">Commentaires & Remarques</span>
                            <div class="text-xs text-content bg-surface p-3.5 rounded-xl border border-border leading-relaxed">
                                {{ $mouvement->commentaire }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Audit info -->
                <div class="pt-6 border-t border-border text-xs text-content-secondary flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        Identifiant d'audit : <span class="font-mono text-content font-semibold">#{{ $mouvement->id }}</span>
                    </div>
                    <div>
                        Saisi par l'utilisateur : <strong class="text-content">{{ $mouvement->user->name ?? 'Système' }}</strong> ({{ $mouvement->user->email ?? 'N/A' }})
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.dashboard>
