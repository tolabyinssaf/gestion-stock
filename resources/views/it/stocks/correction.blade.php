<x-layouts.dashboard title="Correction d'inventaire" :breadcrumbs="['Gestion du stock' => route('it.stocks.index'), 'Correction de stock' => null]">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="wrench" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Régularisation d'inventaire (Correction)</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Ajustement exceptionnel du stock constaté lors d'un inventaire physique (écart, perte, casse ou réconciliation).
                    </p>
                </div>
            </div>

            <a
                href="{{ route('it.stocks.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
            >
                <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                Retour
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs">
                <div class="font-bold mb-1 flex items-center gap-2">
                    <x-ui.icon name="alert-triangle" class="w-4 h-4 text-red-600" />
                    Veuillez corriger les erreurs de saisie :
                </div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div
            class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8"
            x-data="{
                produitId: '{{ old('produit_id', request('produit_id', '')) }}',
                emplacementId: '{{ old('emplacement_id', '') }}',
                nouvelleQuantite: {{ old('nouvelle_quantite', 0) }},
                stockLines: {
                    @foreach($produits as $p)
                        '{{ $p->id }}': {
                            @foreach($p->stocks as $st)
                                '{{ $st->emplacement_id }}': {{ $st->quantite }},
                            @endforeach
                        },
                    @endforeach
                },
                getTheorique() {
                    if (!this.produitId || !this.emplacementId) return 0;
                    if (this.stockLines[this.produitId] && this.stockLines[this.produitId][this.emplacementId] !== undefined) {
                        return this.stockLines[this.produitId][this.emplacementId];
                    }
                    return 0;
                },
                getDifference() {
                    return Number(this.nouvelleQuantite) - this.getTheorique();
                }
            }"
        >
            <form method="POST" action="{{ route('it.stocks.correction.store') }}" class="space-y-6">
                @csrf

                <div class="border-b border-border pb-5 mb-5">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="box" class="w-4 h-4 text-accent" />
                        Article & Localisation de l'inventaire
                    </h3>
                    <p class="text-xs text-content-secondary">Sélectionnez le consommable et l'emplacement concerné par le recomptage physique.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Produit -->
                    <div>
                        <label for="produit_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Produit informatique <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="produit_id"
                            name="produit_id"
                            x-model="produitId"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner un produit...</option>
                            @foreach ($produits as $produit)
                                <option value="{{ $produit->id }}">
                                    [{{ $produit->reference }}] {{ $produit->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Emplacement -->
                    <div>
                        <label for="emplacement_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Emplacement inventorié <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="emplacement_id"
                            name="emplacement_id"
                            x-model="emplacementId"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'emplacement...</option>
                            @foreach ($emplacements as $emp)
                                <option value="{{ $emp->id }}" {{ old('emplacement_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nom }} {{ $emp->site ? '('.$emp->site.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Simulation d'écart / Différence -->
                <div class="p-5 bg-surface rounded-xl border border-border" x-show="produitId && emplacementId">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div class="p-3 bg-white rounded-lg border border-border">
                            <span class="block text-[11px] font-semibold text-content-secondary uppercase">Stock théorique actuel</span>
                            <span class="text-lg font-bold text-content" x-text="getTheorique() + ' unité(s)'"></span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-border">
                            <span class="block text-[11px] font-semibold text-content-secondary uppercase">Stock réel constaté</span>
                            <span class="text-lg font-bold text-primary" x-text="nouvelleQuantite + ' unité(s)'"></span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-border">
                            <span class="block text-[11px] font-semibold text-content-secondary uppercase">Écart d'ajustement</span>
                            <span
                                class="text-lg font-bold"
                                :class="getDifference() > 0 ? 'text-emerald-600' : (getDifference() < 0 ? 'text-rose-600' : 'text-content-secondary')"
                                x-text="(getDifference() > 0 ? '+' : '') + getDifference() + ' unité(s)'"
                            ></span>
                        </div>
                    </div>
                </div>

                <div class="border-b border-border pb-5 mb-5 pt-4">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="clipboard-check" class="w-4 h-4 text-accent" />
                        Comptage physique & Justification obligatoire
                    </h3>
                    <p class="text-xs text-content-secondary">Toute régularisation d'inventaire exige un motif précis pour préserver l'auditabilité.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nouvelle quantité constatée -->
                    <div>
                        <label for="nouvelle_quantite" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Nouvelle quantité réelle en stock <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="nouvelle_quantite"
                            name="nouvelle_quantite"
                            min="0"
                            x-model="nouvelleQuantite"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <!-- Date de mouvement -->
                    <div>
                        <label for="date_mouvement" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Date de l'inventaire <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            id="date_mouvement"
                            name="date_mouvement"
                            value="{{ old('date_mouvement', now()->format('Y-m-d\TH:i')) }}"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <!-- Motif obligatoire -->
                    <div class="md:col-span-2">
                        <label for="motif" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Motif obligatoire de la régularisation <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="motif"
                            name="motif"
                            value="{{ old('motif') }}"
                            required
                            placeholder="ex: Inventaire semestriel du 10/11, perte constatée, casse matérielle..."
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <!-- Commentaire -->
                    <div class="md:col-span-2">
                        <label for="commentaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Explications détaillées
                        </label>
                        <textarea
                            id="commentaire"
                            name="commentaire"
                            rows="2"
                            placeholder="Détails sur l'origine de l'écart..."
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >{{ old('commentaire') }}</textarea>
                    </div>
                </div>

                <!-- Form actions -->
                <div class="pt-6 border-t border-border flex items-center justify-end gap-3">
                    <a
                        href="{{ route('it.stocks.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        Annuler
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
                    >
                        <x-ui.icon name="check" class="w-4 h-4" />
                        Appliquer la correction
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.dashboard>
