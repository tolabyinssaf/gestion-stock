<x-layouts.dashboard title="Transfert de stock" :breadcrumbs="['Gestion du stock' => route('it.stocks.index'), 'Transfert de stock' => null]">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="arrow-right-left" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Transfert inter-emplacements</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Déplacer des matériels ou équipements d'un emplacement source vers un emplacement de destination sans altérer le stock global de l'entreprise.
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
                sourceId: '{{ old('emplacement_source_id', '') }}',
                destinationId: '{{ old('emplacement_destination_id', '') }}',
                isIndividuel: false,
                productsMap: {
                    @foreach($produits as $p)
                        '{{ $p->id }}': {
                            type: '{{ $p->type_suivi }}',
                            ref: '{{ $p->reference }}',
                            nom: '{{ addslashes($p->designation) }}'
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
                getAvailableInSource() {
                    if (!this.produitId || !this.sourceId) return null;
                    if (this.stockLines[this.produitId] && this.stockLines[this.produitId][this.sourceId] !== undefined) {
                        return this.stockLines[this.produitId][this.sourceId];
                    }
                    return 0;
                }
            }"
        >
            <form method="POST" action="{{ route('it.stocks.transfert.store') }}" class="space-y-6">
                @csrf

                <div class="border-b border-border pb-5 mb-5">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="box" class="w-4 h-4 text-primary" />
                        Article à transférer
                    </h3>
                    <p class="text-xs text-content-secondary">Choisissez l'article ou l'équipement à réaffecter d'un local à un autre.</p>
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

                    <!-- Mode Individuel: Équipement à transférer -->
                    <div class="md:col-span-2 p-4 bg-blue-50/50 border border-blue-200/60 rounded-xl" x-show="isIndividuel" style="display: none;">
                        <label for="equipement_id" class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">
                            Équipement individuel (numéro de série) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="equipement_id"
                            name="equipement_id"
                            class="w-full px-3.5 py-2.5 bg-white border border-border rounded-xl text-xs text-content focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'équipement à déplacer...</option>
                            @foreach ($equipementsIndividuels as $eq)
                                <option value="{{ $eq->id }}" {{ old('equipement_id') == $eq->id ? 'selected' : '' }}>
                                    N° Série: {{ $eq->numero_serie }} {{ $eq->code_inventaire ? '('.$eq->code_inventaire.')' : '' }} &mdash; Actuellement à: {{ $eq->emplacement->nom ?? 'Emplacement non défini' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantité à transférer (Mode Quantité) -->
                    <div class="md:col-span-2" x-show="!isIndividuel">
                        <label for="quantite" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Quantité d'unités à déplacer <span class="text-rose-500">*</span>
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
                        <x-ui.icon name="map-pin" class="w-4 h-4 text-primary" />
                        Emplacements Source & Destination
                    </h3>
                    <p class="text-xs text-content-secondary">L'emplacement de départ et d'arrivée doivent obligatoirement être différents.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Emplacement Source -->
                    <div>
                        <label for="emplacement_source_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Emplacement source (Origine) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="emplacement_source_id"
                            name="emplacement_source_id"
                            x-model="sourceId"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'emplacement de départ...</option>
                            @foreach ($emplacements as $emp)
                                <option value="{{ $emp->id }}" {{ old('emplacement_source_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nom }} {{ $emp->site ? '('.$emp->site.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="!isIndividuel && getAvailableInSource() !== null" class="mt-1.5 text-[11px]">
                            Disponibilité actuelle à la source :
                            <strong :class="getAvailableInSource() > 0 ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold'" x-text="getAvailableInSource() + ' unité(s)'"></strong>
                        </div>
                    </div>

                    <!-- Emplacement Destination -->
                    <div>
                        <label for="emplacement_destination_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Emplacement destination (Arrivée) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="emplacement_destination_id"
                            name="emplacement_destination_id"
                            x-model="destinationId"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'emplacement d'arrivée...</option>
                            @foreach ($emplacements as $emp)
                                <option value="{{ $emp->id }}" {{ old('emplacement_destination_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nom }} {{ $emp->site ? '('.$emp->site.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="sourceId && destinationId && sourceId === destinationId" class="mt-1.5 text-[11px] text-rose-600 font-semibold flex items-center gap-1">
                            <x-ui.icon name="alert-triangle" class="w-3.5 h-3.5" />
                            La destination doit être différente de la source !
                        </div>
                    </div>

                    <!-- Date de mouvement -->
                    <div>
                        <label for="date_mouvement" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Date du transfert <span class="text-rose-500">*</span>
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

                    <!-- Motif du transfert -->
                    <div>
                        <label for="motif" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Motif du transfert
                        </label>
                        <input
                            type="text"
                            id="motif"
                            name="motif"
                            value="{{ old('motif') }}"
                            placeholder="ex: Réaménagement bureau, rééquilibrage de stock stock principal..."
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <!-- Commentaire -->
                    <div class="md:col-span-2">
                        <label for="commentaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Observations complémentaires
                        </label>
                        <textarea
                            id="commentaire"
                            name="commentaire"
                            rows="2"
                            placeholder="Remarques éventuelles sur l'état ou le transport..."
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
                        :disabled="sourceId && destinationId && sourceId === destinationId"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <x-ui.icon name="check" class="w-4 h-4" />
                        Confirmer le transfert
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.dashboard>
