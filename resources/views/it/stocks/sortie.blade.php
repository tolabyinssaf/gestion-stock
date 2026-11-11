<x-layouts.dashboard title="Sortie de stock" :breadcrumbs="['Gestion du stock' => route('it.stocks.index'), 'Sortie de stock' => null]">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="arrow-up-right" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Enregistrer une sortie de stock</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Consigner la distribution d'articles consommables ou l'attribution/déclassement d'équipements avec contrôle strict des disponibilités.
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
                emplacementSourceId: '{{ old('emplacement_source_id', '') }}',
                isIndividuel: false,
                productsMap: {
                    @foreach($produits as $p)
                        '{{ $p->id }}': {
                            type: '{{ $p->type_suivi }}',
                            ref: '{{ $p->reference }}',
                            nom: '{{ addslashes($p->designation) }}',
                            totalStock: {{ $p->total_stock }}
                        },
                    @endforeach
                },
                stockLines: {
                    @foreach($produits as $p)
                        '{{ $p->id }}': {
                            @foreach($p->stocks as $st)
                                '{{ $st->emplacement_id }}': {{ $st->quantite }},
                            @endforeach
                        },
                    @endforeach
                },
                init() {
                    this.updateProductType();
                },
                updateProductType() {
                    if (this.produitId && this.productsMap[this.produitId]) {
                        this.isIndividuel = (this.productsMap[this.produitId].type === 'individuel');
                    } else {
                        this.isIndividuel = false;
                    }
                },
                getAvailableStock() {
                    if (!this.produitId || !this.emplacementSourceId) return null;
                    if (this.stockLines[this.produitId] && this.stockLines[this.produitId][this.emplacementSourceId] !== undefined) {
                        return this.stockLines[this.produitId][this.emplacementSourceId];
                    }
                    return 0;
                }
            }"
        >
            <form method="POST" action="{{ route('it.stocks.sortie.store') }}" class="space-y-6">
                @csrf

                <div class="border-b border-border pb-5 mb-5">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="box" class="w-4 h-4 text-primary" />
                        Article & Origine du prélèvement
                    </h3>
                    <p class="text-xs text-content-secondary">Sélectionnez le matériel à sortir et l'emplacement de prélèvement.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Produit -->
                    <div class="md:col-span-2">
                        <label for="produit_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Produit informatique <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="produit_id"
                            name="produit_id"
                            x-model="produitId"
                            @change="updateProductType()"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner un produit...</option>
                            @foreach ($produits as $produit)
                                <option value="{{ $produit->id }}">
                                    [{{ $produit->reference }}] {{ $produit->designation }} &mdash; (Suivi: {{ $produit->type_suivi_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode Individuel: Équipement à sortir -->
                    <div class="md:col-span-2 p-4 bg-orange-50/50 border border-orange-200/60 rounded-xl" x-show="isIndividuel" style="display: none;">
                        <label for="equipement_id" class="block text-xs font-bold text-accent uppercase tracking-wider mb-2">
                            Équipement disponible à sortir (numéro de série) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="equipement_id"
                            name="equipement_id"
                            class="w-full px-3.5 py-2.5 bg-white border border-border rounded-xl text-xs text-content focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'unité disponible...</option>
                            @foreach ($equipementsDisponibles as $eq)
                                <option value="{{ $eq->id }}" {{ old('equipement_id') == $eq->id ? 'selected' : '' }}>
                                    N° Série: {{ $eq->numero_serie }} {{ $eq->code_inventaire ? '('.$eq->code_inventaire.')' : '' }} &mdash; Emplacement actuel: {{ $eq->emplacement->nom ?? 'Non défini' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-content-secondary mt-1.5">
                            Pour ce modèle à suivi individuel, la sortie affectera le statut de l'équipement à "En utilisation".
                        </p>
                    </div>

                    <!-- Emplacement Source (Mode Quantité) -->
                    <div x-show="!isIndividuel">
                        <label for="emplacement_source_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Emplacement source <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="emplacement_source_id"
                            name="emplacement_source_id"
                            x-model="emplacementSourceId"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'emplacement de retrait...</option>
                            @foreach ($emplacements as $emp)
                                <option value="{{ $emp->id }}" {{ old('emplacement_source_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nom }} {{ $emp->site ? '('.$emp->site.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="getAvailableStock() !== null" class="mt-1.5 text-[11px]">
                            Disponibilité à cet emplacement :
                            <strong :class="getAvailableStock() > 0 ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold'" x-text="getAvailableStock() + ' unité(s)'"></strong>
                        </div>
                    </div>

                    <!-- Quantité à sortir (Mode Quantité) -->
                    <div x-show="!isIndividuel">
                        <label for="quantite" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Quantité à sortir <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="quantite"
                            name="quantite"
                            min="1"
                            value="{{ old('quantite', 1) }}"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>
                </div>

                <div class="border-b border-border pb-5 mb-5 pt-4">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="user" class="w-4 h-4 text-primary" />
                        Bénéficiaire & Justification
                    </h3>
                    <p class="text-xs text-content-secondary">Traçabilité de la destination du matériel et du motif de sortie.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Bénéficiaire -->
                    <div>
                        <label for="beneficiaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Bénéficiaire / Collaborateur / Service <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="beneficiaire"
                            name="beneficiaire"
                            value="{{ old('beneficiaire') }}"
                            required
                            placeholder="ex: Jean Dupont (Service Comptabilité)"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <!-- Motif de sortie -->
                    <div>
                        <label for="motif" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Motif de la sortie <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="motif"
                            name="motif"
                            value="{{ old('motif') }}"
                            required
                            placeholder="ex: Dotation nouvel arrivant, remplacement matériel défectueux..."
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <!-- Date de mouvement -->
                    <div>
                        <label for="date_mouvement" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Date de sortie <span class="text-rose-500">*</span>
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

                    <!-- Commentaire -->
                    <div>
                        <label for="commentaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Observations / Remarques
                        </label>
                        <input
                            type="text"
                            id="commentaire"
                            name="commentaire"
                            value="{{ old('commentaire') }}"
                            placeholder="ex: Ticket support n°4821, bon de sortie..."
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
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
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                    >
                        <x-ui.icon name="check" class="w-4 h-4" />
                        Confirmer la sortie de stock
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.dashboard>
