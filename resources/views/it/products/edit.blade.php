<x-layouts.dashboard title="Modifier le matériel" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), $product->nom => route('it.products.show', $product), 'Modifier' => null]">

    <div
        x-data="{
            typeSuivi: '{{ old('type_suivi', $product->type_suivi) }}',
            photoPreview: '{{ $product->photo_url }}',
            handlePhotoChange(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }"
        class="max-w-4xl mx-auto"
    >

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
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a
                    href="{{ route('it.products.show', $product) }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-content-secondary hover:text-primary transition-colors mb-2"
                >
                    <x-ui.icon name="chevron-left" class="w-4 h-4" />
                    Retour à la fiche produit
                </a>
                <h1 class="text-2xl font-bold text-content tracking-tight">Modifier la référence</h1>
                <p class="text-xs sm:text-sm text-content-secondary mt-1">
                    Mise à jour des informations du produit <strong class="text-content font-semibold">« {{ $product->nom }} »</strong> (SKU: {{ $product->reference }})
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('it.products.show', $product) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="eye" class="w-4 h-4 text-content-secondary" />
                    Voir la fiche
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <form
            method="POST"
            action="{{ route('it.products.update', $product) }}"
            enctype="multipart/form-data"
            class="space-y-8"
        >
            @csrf
            @method('PUT')

            <!-- SECTION 1: IDENTIFICATION PRINCIPALE -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="package" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">1. Identification principale</h2>
                        <p class="text-xs text-content-secondary">Informations fondamentales d'identification du produit</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Désignation du produit <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="{{ old('nom', $product->nom) }}"
                            required
                            placeholder="Ex: PC Portable ThinkPad T14s Gen 4..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('nom') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('nom')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Marque -->
                    <div>
                        <label for="marque" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Marque / Constructeur
                        </label>
                        <input
                            type="text"
                            id="marque"
                            name="marque"
                            value="{{ old('marque', $product->marque) }}"
                            placeholder="Ex: Dell, Lenovo, Apple, HP..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('marque') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('marque')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Modèle -->
                    <div>
                        <label for="modele" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Modèle précis
                        </label>
                        <input
                            type="text"
                            id="modele"
                            name="modele"
                            value="{{ old('modele', $product->modele) }}"
                            placeholder="Ex: Latitude 5440, ThinkPad T14..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('modele') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('modele')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Référence SKU interne -->
                    <div>
                        <label for="reference" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Référence interne / SKU <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="reference"
                            name="reference"
                            value="{{ old('reference', $product->reference) }}"
                            required
                            placeholder="Ex: PC-LEN-T14-01..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs font-mono text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all uppercase @error('reference') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('reference')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label for="categorie_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="categorie_id"
                            name="categorie_id"
                            required
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('categorie_id') border-red-300 ring-2 ring-red-100 @enderror"
                        >
                            <option value="">Sélectionnez une catégorie...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string)old('categorie_id', $product->categorie_id) === (string)$cat->id ? 'selected' : '' }}>
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: MODE DE TRAÇABILITÉ & GESTION -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="tag" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">2. Mode de traçabilité & Gestion</h2>
                        <p class="text-xs text-content-secondary">Déterminez comment les unités sont suivies dans les stocks</p>
                    </div>
                </div>

                <!-- Type de suivi Cards -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-content uppercase tracking-wider mb-3">
                        Type de suivi en inventaire <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Option 1: Individuel / Sérialisé -->
                        <label
                            class="relative flex flex-col p-5 border-2 rounded-2xl cursor-pointer transition-all"
                            :class="typeSuivi === 'individuel' ? 'border-primary bg-primary/5 ring-2 ring-primary/10' : 'border-border bg-white hover:bg-surface/50'"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="typeSuivi === 'individuel' ? 'bg-primary text-white' : 'bg-surface text-content-secondary'">
                                        <x-ui.icon name="tag" class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-content">Suivi individuel sérialisé</h3>
                                        <span class="text-[11px] font-semibold text-primary">Unité par unité (Numéro de série)</span>
                                    </div>
                                </div>
                                <input
                                    type="radio"
                                    name="type_suivi"
                                    value="individuel"
                                    x-model="typeSuivi"
                                    class="w-4 h-4 text-primary border-border focus:ring-primary/20 mt-1"
                                />
                            </div>
                            <p class="text-xs text-content-secondary mt-3 leading-relaxed">
                                Chaque exemplaire possède une identité propre, un numéro de série unique et un statut individuel (ordinateurs, écrans, téléphones, serveurs).
                            </p>
                        </label>

                        <!-- Option 2: Quantitatif / En lot -->
                        <label
                            class="relative flex flex-col p-5 border-2 rounded-2xl cursor-pointer transition-all"
                            :class="typeSuivi === 'quantite' ? 'border-accent bg-accent/5 ring-2 ring-accent/10' : 'border-border bg-white hover:bg-surface/50'"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="typeSuivi === 'quantite' ? 'bg-accent text-white' : 'bg-surface text-content-secondary'">
                                        <x-ui.icon name="boxes" class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-content">Suivi quantitatif en lot</h3>
                                        <span class="text-[11px] font-semibold text-accent">Gestion par volume de stock</span>
                                    </div>
                                </div>
                                <input
                                    type="radio"
                                    name="type_suivi"
                                    value="quantite"
                                    x-model="typeSuivi"
                                    class="w-4 h-4 text-accent border-border focus:ring-accent/20 mt-1"
                                />
                            </div>
                            <p class="text-xs text-content-secondary mt-3 leading-relaxed">
                                Géré uniquement par quantité disponible en stock global (câbles, chargeurs, adaptateurs HDMI, souris d'entrée de gamme).
                            </p>
                        </label>
                    </div>
                    @error('type_suivi')
                        <p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div class="pt-4 border-t border-border">
                    <label for="statut" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                        Statut du produit <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="statut"
                        name="statut"
                        class="block w-full sm:w-64 py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                        <option value="actif" {{ old('statut', $product->statut) === 'actif' ? 'selected' : '' }}>Actif (disponible au catalogue)</option>
                        <option value="archive" {{ old('statut', $product->statut) === 'archive' ? 'selected' : '' }}>Archivé (référence obsolète)</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 3: SPÉCIFICATIONS & VISUEL -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary-light flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="file-text" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">3. Spécifications & Visuel</h2>
                        <p class="text-xs text-content-secondary">Détails techniques et illustration de la référence</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Photo Upload with Preview -->
                    <div>
                        <label class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Photo ou visuel du produit
                        </label>

                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <!-- Image Preview Area -->
                            <div class="w-28 h-28 rounded-2xl border-2 border-dashed border-border bg-surface flex items-center justify-center overflow-hidden flex-shrink-0 relative">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Aperçu" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="flex flex-col items-center justify-center text-content-secondary p-2 text-center">
                                        <x-ui.icon name="image" class="w-7 h-7 text-content-secondary/50 mb-1" />
                                        <span class="text-[10px]">Aucune image</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Upload Input -->
                            <div class="flex-1 w-full">
                                <label
                                    for="photo"
                                    class="flex flex-col items-center justify-center px-6 py-5 border-2 border-dashed border-border rounded-2xl cursor-pointer bg-surface/40 hover:bg-surface/80 transition-all text-center"
                                >
                                    <x-ui.icon name="upload-cloud" class="w-6 h-6 text-primary mb-2" />
                                    <span class="text-xs font-semibold text-content">
                                        Sélectionner une nouvelle photo pour remplacer l'actuelle
                                    </span>
                                    <span class="text-[11px] text-content-secondary mt-1">
                                        Formats acceptés : JPG, PNG, WEBP (Max 2 Mo)
                                    </span>
                                    <input
                                        type="file"
                                        id="photo"
                                        name="photo"
                                        accept="image/jpeg,image/png,image/webp"
                                        @change="handlePhotoChange"
                                        class="sr-only"
                                    />
                                </label>
                                @error('photo')
                                    <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Description générale
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Description courte de l'équipement..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('description') border-red-300 ring-2 ring-red-100 @enderror"
                        >{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Caractéristiques techniques -->
                    <div>
                        <label for="caracteristiques" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Caractéristiques techniques détaillées
                        </label>
                        <textarea
                            id="caracteristiques"
                            name="caracteristiques"
                            rows="4"
                            placeholder="Processeur, RAM, connectique..."
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs font-mono text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('caracteristiques') border-red-300 ring-2 ring-red-100 @enderror"
                        >{{ old('caracteristiques', $product->caracteristiques) }}</textarea>
                        @error('caracteristiques')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 4: PARAMÈTRES D'APPROVISIONNEMENT & COÛT -->
            <div class="bg-white rounded-2xl border border-border p-6 sm:p-8 shadow-xs">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="bar-chart-3" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-content">4. Approvisionnement & Coûts</h2>
                        <p class="text-xs text-content-secondary">Informations financières et seuils d'alerte</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Prix indicatif -->
                    <div>
                        <label for="prix_indicatif" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Prix unitaire indicatif HT (€)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="prix_indicatif"
                                name="prix_indicatif"
                                value="{{ old('prix_indicatif', $product->prix_indicatif) }}"
                                placeholder="0.00"
                                class="block w-full py-2.5 pl-3.5 pr-10 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('prix_indicatif') border-red-300 ring-2 ring-red-100 @enderror"
                            />
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-content-secondary text-xs font-semibold">
                                €
                            </div>
                        </div>
                        @error('prix_indicatif')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seuil minimum d'alerte -->
                    <div>
                        <label for="seuil_minimum" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Seuil d'alerte stock minimum <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            min="0"
                            id="seuil_minimum"
                            name="seuil_minimum"
                            value="{{ old('seuil_minimum', $product->seuil_minimum) }}"
                            required
                            class="block w-full py-2.5 px-3.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('seuil_minimum') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('seuil_minimum')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-white rounded-2xl border border-border shadow-xs flex items-center justify-between gap-4">
                <a
                    href="{{ route('it.products.show', $product) }}"
                    class="px-5 py-2.5 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-light text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
                >
                    <x-ui.icon name="check" class="w-4 h-4" />
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

</x-layouts.dashboard>
