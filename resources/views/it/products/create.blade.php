<x-layouts.dashboard title="Ajouter un matériel" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), 'Ajouter un matériel' => null]">

    <div class="max-w-3xl mx-auto">

        <!-- Flash Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <div class="flex items-center gap-2 mb-2 font-semibold">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0" />
                    <span>Des erreurs sont survenues lors de la validation du formulaire :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <a
                href="{{ route('it.products.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-content-secondary hover:text-primary transition-colors mb-2"
            >
                <x-ui.icon name="chevron-left" class="w-4 h-4" />
                Retour à la gestion du matériel
            </a>
            <h1 class="text-2xl font-bold text-content tracking-tight">Ajouter un matériel</h1>
            <p class="text-xs sm:text-sm text-content-secondary mt-1">
                Renseignez les informations essentielles du produit et sa quantité initiale en stock.
            </p>
        </div>

        <!-- Formulaire simplifié -->
        <form
            method="POST"
            action="{{ route('it.products.store') }}"
            class="space-y-6"
        >
            @csrf

            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs space-y-5">
                
                <!-- 1. Nom du matériel (obligatoire) -->
                <div>
                    <label for="nom" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                        Nom du matériel <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="{{ old('nom') }}"
                        required
                        placeholder="Ex: Clavier sans fil Logitech MX Keys, Écran Dell 27'', PC Portable HP..."
                        class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('nom') border-red-300 ring-2 ring-red-100 @enderror"
                    />
                    @error('nom')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Catégorie (obligatoire) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="categorie_id" class="block text-xs font-bold text-content uppercase tracking-wider">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <a href="{{ route('it.categories.index') }}" class="text-[11px] font-semibold text-primary hover:underline">
                            + Gérer les catégories
                        </a>
                    </div>
                    <select
                        id="categorie_id"
                        name="categorie_id"
                        required
                        class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('categorie_id') border-red-300 ring-2 ring-red-100 @enderror"
                    >
                        <option value="">Sélectionnez une catégorie...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)old('categorie_id') === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- 3. Marque (facultative) -->
                    <div>
                        <label for="marque" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Marque <span class="text-content-secondary font-normal text-[11px]">(facultative)</span>
                        </label>
                        <input
                            type="text"
                            id="marque"
                            name="marque"
                            value="{{ old('marque') }}"
                            placeholder="Ex: Dell, HP, Lenovo, Logitech, Apple..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                    </div>

                    <!-- 4. Modèle ou référence (facultatif) -->
                    <div>
                        <label for="modele" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Modèle ou Référence <span class="text-content-secondary font-normal text-[11px]">(facultatif)</span>
                        </label>
                        <input
                            type="text"
                            id="modele"
                            name="modele"
                            value="{{ old('modele') }}"
                            placeholder="Ex: MX Keys S, U2723QE, ProBook 450..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-border">
                    <!-- 5. Quantité initiale (entier >= 0) -->
                    <div>
                        <label for="quantite_initiale" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Quantité initiale en stock
                        </label>
                        <input
                            type="number"
                            id="quantite_initiale"
                            name="quantite_initiale"
                            value="{{ old('quantite_initiale', 0) }}"
                            min="0"
                            step="1"
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                        <p class="text-[11px] text-content-secondary mt-1">
                            Si supérieure à 0, une entrée sera automatiquement consignée dans l'historique des mouvements.
                        </p>
                    </div>

                    <!-- 6. Emplacement de stockage (si plusieurs disponibles) -->
                    <div>
                        <label for="emplacement_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Emplacement de stockage
                        </label>
                        <select
                            id="emplacement_id"
                            name="emplacement_id"
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                            @foreach ($emplacements as $emp)
                                <option value="{{ $emp->id }}" {{ (string)old('emplacement_id') === (string)$emp->id ? 'selected' : '' }}>
                                    {{ $emp->nom }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-content-secondary mt-1">
                            Lieu physique où le matériel est entreposé.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Boutons de soumission -->
            <div class="p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between gap-4">
                <a
                    href="{{ route('it.products.index') }}"
                    class="px-5 py-2.5 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-accent hover:bg-accent-light text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
                >
                    <x-ui.icon name="check" class="w-4 h-4" />
                    Enregistrer le matériel
                </button>
            </div>
        </form>
    </div>

</x-layouts.dashboard>
