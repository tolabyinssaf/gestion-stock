<x-layouts.dashboard title="Entrée de stock" :breadcrumbs="['Gestion du stock' => route('it.stocks.index'), 'Entrée de stock' => null]">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="arrow-down-left" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Enregistrer une entrée de stock</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Ajoutez des quantités de matériel en stock ou réceptionnez un équipement.
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
                init() {
                    this.updateProductType();
                },
                updateProductType() {
                    if (this.produitId && this.productsMap[this.produitId]) {
                        this.isIndividuel = (this.productsMap[this.produitId].type === 'individuel');
                    } else {
                        this.isIndividuel = false;
                    }
                }
            }"
        >
            <form method="POST" action="{{ route('it.stocks.entree.store') }}" class="space-y-6">
                @csrf

                <div class="border-b border-border pb-5 mb-5">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="box" class="w-4 h-4 text-emerald-600" />
                        Article & Réception
                    </h3>
                    <p class="text-xs text-content-secondary">Sélectionnez le matériel réceptionné à ajouter au stock disponible.</p>
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

                    <!-- Mode Individuel: Équipement à réceptionner -->
                    <div class="md:col-span-2 p-4 bg-blue-50/50 border border-blue-200/60 rounded-xl" x-show="isIndividuel" style="display: none;">
                        <label for="equipement_id" class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">
                            Équipement individuel (numéro de série) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="equipement_id"
                            name="equipement_id"
                            class="w-full px-3.5 py-2.5 bg-white border border-border rounded-xl text-xs text-content focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            <option value="">Sélectionner l'unité à entrer en stock...</option>
                            @foreach ($equipementsIndividuels as $eq)
                                <option value="{{ $eq->id }}" {{ old('equipement_id') == $eq->id ? 'selected' : '' }}>
                                    N° Série: {{ $eq->numero_serie }} {{ $eq->code_inventaire ? '('.$eq->code_inventaire.')' : '' }} &mdash; Actuellement: {{ $eq->statut_label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-content-secondary mt-1.5">
                            Pour ce modèle à suivi individuel, la quantité entrée sera automatiquement fixée à 1 et le statut de l'équipement passera à "Disponible".
                        </p>
                    </div>

                    <!-- Mode Quantité: Champ Quantité -->
                    <div x-show="!isIndividuel">
                        <label for="quantite" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Quantité réceptionnée <span class="text-rose-500">*</span>
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

                    <!-- Situation Destination -->
                    <div>
                        <label class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Situation de destination
                        </label>
                        <input type="hidden" name="situation" value="en_stock">
                        <div class="px-3.5 py-2.5 bg-surface/70 border border-border rounded-xl text-xs text-content flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-bold text-emerald-800">En stock (disponible en réserve)</span>
                        </div>
                    </div>
                </div>

                <div class="border-b border-border pb-5 mb-5 pt-4">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                        <x-ui.icon name="file-text" class="w-4 h-4 text-emerald-600" />
                        Détails & Justificatif
                    </h3>
                    <p class="text-xs text-content-secondary">Informations de traçabilité sur la réception de marchandise.</p>
                </div>

                <div class="space-y-5">
                    <!-- Date de mouvement -->
                    <div>
                        <label for="date_mouvement" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Date de réception <span class="text-rose-500">*</span>
                        </label>
                        @php
                            // Date "maintenant" arrondie à la minute, dans le fuseau applicatif
                            $nowLocal = now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i');
                        @endphp
                        <input
                            type="datetime-local"
                            id="date_mouvement"
                            name="date_mouvement"
                            value="{{ old('date_mouvement', $nowLocal) }}"
                            max="{{ $nowLocal }}"
                            required
                            class="w-full sm:w-1/2 px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                        <p class="text-[11px] text-content-secondary mt-1.5">
                            La date de réception ne peut pas être dans le futur.
                        </p>
                    </div>

                    <!-- Commentaire / Notes -->
                    <div>
                        <label for="commentaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Observations / Remarques
                        </label>
                        <textarea
                            id="commentaire"
                            name="commentaire"
                            rows="3"
                            placeholder="Informations complémentaires, n° de bon de livraison..."
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
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors"
                    >
                        <x-ui.icon name="check" class="w-4 h-4" />
                        Valider l'entrée en stock
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.dashboard>