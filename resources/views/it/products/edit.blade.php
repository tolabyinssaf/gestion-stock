@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'chevron-left'   => 'm15 18-6-6 6-6',
        'eye'            => 'M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0|M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
        'package'        => 'm7.5 4.27 9 5.15|M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'file-text'      => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M10 9H8|M16 13H8|M16 17H8',
        'bar-chart-3'    => 'M3 3v16a2 2 0 0 0 2 2h16|M18 17V9|M13 17V5|M8 17v-3',
        'image'          => 'M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z|M9 9h.01|m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21',
        'upload-cloud'   => 'M12 13v8|M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242|m8 17 4-4 4 4',
        'check'          => 'M20 6 9 17l-5-5',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Modifier le matériel" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), $product->nom => route('it.products.show', $product), 'Modifier' => null]">

    <div
        x-data="{
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

        <!-- Erreurs de validation -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 rounded-2xl text-rose-800 text-sm">
                <div class="flex items-start gap-2.5 mb-2 font-semibold">
                    {!! $svg('alert-triangle', 'w-5 h-5 text-rose-600 flex-shrink-0') !!}
                    <span>Des erreurs sont survenues lors de la validation du formulaire :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- En-tête -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="min-w-0">
                <a
                    href="{{ route('it.products.show', $product) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] hover:text-[#0092FD] transition-all mb-4"
                >
                    {!! $svg('chevron-left', 'w-4 h-4') !!}
                    Retour à la fiche produit
                </a>
                <h1 class="text-xl sm:text-2xl font-extrabold text-content tracking-tight">Modifier le matériel</h1>
                <p class="text-xs sm:text-sm text-content-secondary mt-1 break-words">
                    Mise à jour des informations du matériel <strong class="text-content font-semibold">« {{ $product->nom }} »</strong> (Réf : {{ $product->reference }})
                </p>
            </div>

            <div class="flex items-center gap-2 sm:pt-1">
                <a
                    href="{{ route('it.products.show', $product) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] transition-all w-full sm:w-auto"
                >
                    {!! $svg('eye', 'w-4 h-4 text-[#0092FD]') !!}
                    Voir la fiche
                </a>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('it.products.update', $product) }}"
            enctype="multipart/form-data"
            class="space-y-5 sm:space-y-6"
        >
            @csrf
            @method('PUT')

            <!-- 1. Identification -->
            <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border/60">
                    <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                        {!! $svg('package') !!}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-content">1. Identification du matériel</h2>
                        <p class="text-xs text-content-secondary">Informations générales du matériel</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Nom du matériel <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="{{ old('nom', $product->nom) }}"
                            required
                            placeholder="Ex: PC Portable ThinkPad T14s Gen 4..."
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('nom') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('nom')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>

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
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('marque') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('marque')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>

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
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('modele') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('modele')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>

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
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs font-mono text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all uppercase @error('reference') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('reference')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="categorie_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="categorie_id"
                            name="categorie_id"
                            required
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('categorie_id') border-red-300 ring-2 ring-red-100 @enderror"
                        >
                            <option value="">Sélectionnez une catégorie...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string)old('categorie_id', $product->categorie_id) === (string)$cat->id ? 'selected' : '' }}>
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 2. Spécifications & Visuel -->
            <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border/60">
                    <div class="w-11 h-11 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                        {!! $svg('file-text') !!}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-content">2. Spécifications & Visuel</h2>
                        <p class="text-xs text-content-secondary">Détails techniques et illustration de la référence</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Photo ou visuel du produit
                        </label>

                        <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                            <div class="w-28 h-28 rounded-3xl bg-surface/70 flex items-center justify-center overflow-hidden flex-shrink-0 relative">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Aperçu" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="flex flex-col items-center justify-center text-content-secondary p-2 text-center">
                                        {!! $svg('image', 'w-7 h-7 text-content-secondary/50 mb-1') !!}
                                        <span class="text-[10px]">Aucune image</span>
                                    </div>
                                </template>
                            </div>

                            <div class="flex-1 w-full">
                                <label
                                    for="photo"
                                    class="flex flex-col items-center justify-center px-5 sm:px-6 py-5 border-2 border-dashed border-[#0092FD]/30 rounded-3xl cursor-pointer bg-[#0092FD]/5 hover:bg-[#0092FD]/10 transition-all text-center"
                                >
                                    <span class="w-10 h-10 rounded-full bg-white text-[#0092FD] flex items-center justify-center mb-2 shadow-[0_2px_10px_rgba(2,45,119,0.08)]">
                                        {!! $svg('upload-cloud', 'w-5 h-5') !!}
                                    </span>
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
                                    <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Description générale
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Description courte de l'équipement..."
                            class="block w-full py-3 px-4 bg-surface/60 border border-transparent rounded-2xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('description') border-red-300 ring-2 ring-red-100 @enderror"
                        >{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="caracteristiques" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Caractéristiques techniques détaillées
                        </label>
                        <textarea
                            id="caracteristiques"
                            name="caracteristiques"
                            rows="4"
                            placeholder="Processeur, RAM, connectique..."
                            class="block w-full py-3 px-4 bg-surface/60 border border-transparent rounded-2xl text-xs font-mono text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('caracteristiques') border-red-300 ring-2 ring-red-100 @enderror"
                        >{{ old('caracteristiques', $product->caracteristiques) }}</textarea>
                        @error('caracteristiques')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 3. Approvisionnement & Coûts -->
            <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-border/60">
                    <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        {!! $svg('bar-chart-3') !!}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-content">3. Approvisionnement & Coûts</h2>
                        <p class="text-xs text-content-secondary">Informations financières et seuils d'alerte</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
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
                                class="block w-full py-2.5 pl-4 pr-10 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('prix_indicatif') border-red-300 ring-2 ring-red-100 @enderror"
                            />
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-content-secondary text-xs font-semibold">
                                €
                            </div>
                        </div>
                        @error('prix_indicatif')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>

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
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('seuil_minimum') border-red-300 ring-2 ring-red-100 @enderror"
                        />
                        @error('seuil_minimum')
                            <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="p-4 sm:p-5 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <a
                    href="{{ route('it.products.show', $product) }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-surface hover:bg-slate-100 rounded-full text-xs font-semibold text-content transition-colors"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-bold rounded-full shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors cursor-pointer"
                >
                    {!! $svg('check', 'w-4 h-4') !!}
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

</x-layouts.dashboard>