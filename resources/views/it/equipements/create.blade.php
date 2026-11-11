<x-layouts.dashboard title="Nouvel équipement" :breadcrumbs="['Équipements' => route('it.equipements.index'), 'Nouvel équipement' => null]">

    <div
        x-data="{
            produits: @js($produits),
            selectedProduitId: '{{ old('produit_id') }}',
            etatPhysique: '{{ old('etat_physique', 'bon_etat') }}',
            statutUtilisation: '{{ old('statut_utilisation', 'disponible') }}',
            get selectedProduit() {
                return this.produits.find(p => p.id == this.selectedProduitId) || null;
            },
            get isIncoherent() {
                return (this.etatPhysique === 'hors_service' || this.etatPhysique === 'en_panne') && this.statutUtilisation === 'disponible';
            }
        }"
        class="max-w-4xl mx-auto"
    >

        <!-- Flash Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <div class="flex items-center gap-2 mb-2 font-semibold">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0" />
                    <span>Des erreurs sont survenues lors de la validation :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Incoherence Alert (Alpine dynamic) -->
        <div
            x-show="isIncoherent"
            x-transition
            class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm flex items-start gap-3"
            style="display: none;"
        >
            <x-ui.icon name="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
            <div>
                <strong class="font-bold">Attention à la cohérence de l'équipement :</strong>
                <p class="text-xs mt-0.5">Un matériel en panne ou hors service ne peut pas avoir le statut d'utilisation « Disponible ». Veuillez ajuster son statut.</p>
            </div>
        </div>

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a
                    href="{{ route('it.equipements.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-content-secondary hover:text-primary transition-colors mb-2"
                >
                    <x-ui.icon name="chevron-left" class="w-4 h-4" />
                    Retour aux équipements
                </a>
                <h1 class="text-2xl font-bold text-content tracking-tight">Enregistrer un équipement physique</h1>
                <p class="text-xs sm:text-sm text-content-secondary mt-1">
                    Affectez un code d'inventaire et un numéro de série à une unité matérielle appartenant au catalogue.
                </p>
            </div>
        </div>

        <!-- Form -->
        <form
            method="POST"
            action="{{ route('it.equipements.store') }}"
            class="space-y-8"
        >
            @csrf

            <!-- SECTION 1: IDENTIFICATION & MODÈLE -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="laptop" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">1. Identification & Modèle du catalogue</h2>
                        <p class="text-xs text-content-secondary">Associez cet exemplaire physique à une référence du catalogue</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Produit Select -->
                    <div>
                        <label for="produit_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Modèle du catalogue (Suivi sérialisé) <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="produit_id"
                            name="produit_id"
                            x-model="selectedProduitId"
                            required
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('produit_id') border-red-300 ring-2 ring-red-100 @enderror"
                        >
                            <option value="">Sélectionnez un modèle de matériel...</option>
                            @foreach ($produits as $prod)
                                <option value="{{ $prod->id }}" {{ (string)old('produit_id') === (string)$prod->id ? 'selected' : '' }}>
                                    {{ $prod->nom }} ({{ $prod->marque }} &bull; SKU: {{ $prod->reference }})
                                </option>
                            @endforeach
                        </select>
                        @error('produit_id')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selected Product Dynamic Card (Alpine.js) -->
                    <template x-if="selectedProduit">
                        <div class="p-4 rounded-xl bg-surface border border-border flex items-center gap-4 transition-all">
                            <div class="w-14 h-14 rounded-xl bg-white border border-border overflow-hidden flex items-center justify-center flex-shrink-0">
                                <template x-if="selectedProduit.photo">
                                    <img :src="'/storage/' + selectedProduit.photo" :alt="selectedProduit.nom" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!selectedProduit.photo">
                                    <x-ui.icon name="laptop" class="w-7 h-7 text-primary" />
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-content truncate" x-text="selectedProduit.nom"></h4>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-primary-light border border-blue-200">
                                        Sérialisé
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-content-secondary mt-1 flex-wrap">
                                    <span>Marque : <strong class="text-content font-medium" x-text="selectedProduit.marque || '—'"></strong></span>
                                    <span>&bull;</span>
                                    <span>Modèle : <strong class="text-content font-medium" x-text="selectedProduit.modele || '—'"></strong></span>
                                    <span>&bull;</span>
                                    <span>SKU : <code class="font-mono font-semibold text-content" x-text="selectedProduit.reference"></code></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4 border-t border-border">
                        <!-- Code d'inventaire -->
                        <div>
                            <label for="code_inventaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                Code d'inventaire interne <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="code_inventaire"
                                name="code_inventaire"
                                value="{{ old('code_inventaire', $suggestedCode) }}"
                                required
                                placeholder="Ex: EQ-2026-00001"
                                class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs font-mono font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all uppercase @error('code_inventaire') border-red-300 ring-2 ring-red-100 @enderror"
                            />
                            <p class="text-[10px] text-content-secondary mt-1">Généré automatiquement (modifiable si nécessaire).</p>
                            @error('code_inventaire')
                                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Numéro de série -->
                        <div>
                            <label for="numero_serie" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                Numéro de série (S/N)
                            </label>
                            <input
                                type="text"
                                id="numero_serie"
                                name="numero_serie"
                                value="{{ old('numero_serie') }}"
                                placeholder="Ex: CN-0H789G-12345"
                                class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs font-mono text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('numero_serie') border-red-300 ring-2 ring-red-100 @enderror"
                            />
                            <p class="text-[10px] text-content-secondary mt-1">Numéro constructeur (doit être unique si renseigné).</p>
                            @error('numero_serie')
                                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date d'entrée -->
                        <div>
                            <label for="date_entree" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                Date d'entrée en stock
                            </label>
                            <input
                                type="date"
                                id="date_entree"
                                name="date_entree"
                                value="{{ old('date_entree', date('Y-m-d')) }}"
                                class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('date_entree') border-red-300 ring-2 ring-red-100 @enderror"
                            />
                            @error('date_entree')
                                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: SITUATION & EMPLACEMENT -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="map-pin" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">2. Situation & Emplacement</h2>
                        <p class="text-xs text-content-secondary">État opérationnel et lieu de stockage du matériel</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- État physique -->
                    <div>
                        <label for="etat_physique" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            État physique <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="etat_physique"
                            name="etat_physique"
                            x-model="etatPhysique"
                            required
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('etat_physique') border-red-300 ring-2 ring-red-100 @enderror"
                        >
                            @foreach (\App\Models\Equipement::ETATS_PHYSIQUES as $key => $label)
                                <option value="{{ $key }}" {{ old('etat_physique', 'bon_etat') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('etat_physique')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut d'utilisation -->
                    <div>
                        <label for="statut_utilisation" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Statut d'utilisation <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="statut_utilisation"
                            name="statut_utilisation"
                            x-model="statutUtilisation"
                            required
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('statut_utilisation') border-red-300 ring-2 ring-red-100 @enderror"
                        >
                            @foreach (\App\Models\Equipement::STATUTS_UTILISATION as $key => $label)
                                <option value="{{ $key }}" {{ old('statut_utilisation', 'disponible') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('statut_utilisation')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emplacement -->
                    <div>
                        <label for="emplacement_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Emplacement de stockage
                        </label>
                        <select
                            id="emplacement_id"
                            name="emplacement_id"
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('emplacement_id') border-red-300 ring-2 ring-red-100 @enderror"
                        >
                            <option value="">Non assigné (ou en transit)</option>
                            @foreach ($emplacements as $loc)
                                <option value="{{ $loc->id }}" {{ (string)old('emplacement_id') === (string)$loc->id ? 'selected' : '' }}>
                                    {{ $loc->nom }} ({{ $loc->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('emplacement_id')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 3: INFORMATIONS COMPLÉMENTAIRES -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary-light flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="file-text" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">3. Informations complémentaires</h2>
                        <p class="text-xs text-content-secondary">Remarques, particularités ou historique préalable</p>
                    </div>
                </div>

                <div>
                    <label for="observations" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                        Observations / Notes techniques
                    </label>
                    <textarea
                        id="observations"
                        name="observations"
                        rows="4"
                        placeholder="Précisez ici les détails notables (ex: légères rayures sur le capot, livré avec housse et adaptateur, station d'accueil spécifique...)"
                        class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('observations') border-red-300 ring-2 ring-red-100 @enderror"
                    >{{ old('observations') }}</textarea>
                    @error('observations')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between gap-4">
                <a
                    href="{{ route('it.equipements.index') }}"
                    class="px-5 py-2.5 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-accent hover:bg-accent-light text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
                >
                    <x-ui.icon name="check" class="w-4 h-4" />
                    Enregistrer l'équipement
                </button>
            </div>
        </form>
    </div>

</x-layouts.dashboard>
