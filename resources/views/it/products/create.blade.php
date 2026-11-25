@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'chevron-left'   => 'm15 18-6-6 6-6',
        'check'          => 'M20 6 9 17l-5-5',
        'plus'           => 'M5 12h14|M12 5v14',
        'package-plus'   => 'M16 16h6|M19 13v6|M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14|m7.5 4.27 9 5.15|M3.29 7 12 12l8.71-5|M12 22V12',
        'image'          => 'M3 3h18v18H3z|M9 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4|m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Ajouter un matériel" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), 'Ajouter un matériel' => null]">

    <div class="max-w-3xl mx-auto">

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
        <div class="mb-6">
            <a
                href="{{ route('it.products.index') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] hover:text-[#0092FD] transition-all mb-4"
            >
                {!! $svg('chevron-left', 'w-4 h-4') !!}
                Retour à la gestion du matériel
            </a>

            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('package-plus', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-content tracking-tight">Ajouter un matériel</h1>
                    <p class="text-xs sm:text-sm text-content-secondary mt-0.5">
                        Renseignez les informations essentielles du produit et sa quantité initiale en stock.
                    </p>
                </div>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('it.products.store') }}"
            enctype="multipart/form-data"
            class="space-y-5 sm:space-y-6"
        >
            @csrf

            <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-[0_2px_14px_rgba(2,45,119,0.06)] space-y-5">

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
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('nom') border-red-300 ring-2 ring-red-100 @enderror"
                    />
                    @error('nom')
                        <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label for="categorie_id" class="block text-xs font-bold text-content uppercase tracking-wider">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <a href="{{ route('it.categories.index') }}" class="text-[11px] font-semibold text-[#0092FD] hover:underline whitespace-nowrap">
                            + Gérer les catégories
                        </a>
                    </div>
                    <select
                        id="categorie_id"
                        name="categorie_id"
                        required
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all @error('categorie_id') border-red-300 ring-2 ring-red-100 @enderror"
                    >
                        <option value="">Sélectionnez une catégorie...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)old('categorie_id') === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="marque" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Marque <span class="text-content-secondary font-normal text-[11px] normal-case">(facultative)</span>
                        </label>
                        <input
                            type="text"
                            id="marque"
                            name="marque"
                            value="{{ old('marque') }}"
                            placeholder="Ex: Dell, HP, Lenovo, Logitech, Apple..."
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                        />
                    </div>

                    <div>
                        <label for="modele" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Modèle ou Référence <span class="text-content-secondary font-normal text-[11px] normal-case">(facultatif)</span>
                        </label>
                        <input
                            type="text"
                            id="modele"
                            name="modele"
                            value="{{ old('modele') }}"
                            placeholder="Ex: MX Keys S, U2723QE, ProBook 450..."
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                        />
                    </div>
                </div>

                <!-- Image du produit -->
                <div class="pt-5 border-t border-border/60">
                    <label for="photo" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                        Image du produit <span class="text-content-secondary font-normal text-[11px] normal-case">(facultative)</span>
                    </label>

                    <div class="flex items-start gap-4">
                        <!-- Aperçu -->
                        <div class="w-24 h-24 rounded-2xl bg-surface/60 border border-border/60 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img id="photo-preview" src="" alt="" class="w-full h-full object-cover hidden">
                            <svg id="photo-placeholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                 class="w-8 h-8 text-content-secondary/50">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="9" cy="9" r="2"/>
                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                            </svg>
                        </div>

                        <!-- Input -->
                        <div class="flex-1 min-w-0">
                            <input
                                type="file"
                                id="photo"
                                name="photo"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                class="block w-full text-xs text-content file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#0092FD]/10 file:text-[#0092FD] hover:file:bg-[#0092FD]/20 file:cursor-pointer cursor-pointer bg-surface/60 rounded-full border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 @error('photo') border-red-300 ring-2 ring-red-100 @enderror"
                            />
                            <p class="text-[11px] text-content-secondary mt-1.5 px-2">
                                Formats acceptés : JPG, PNG, WEBP. Taille max : 2 Mo.
                            </p>
                            @error('photo')
                                <p class="text-[11px] text-red-600 mt-1.5 px-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-5 border-t border-border/60">
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
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                        />
                        <p class="text-[11px] text-content-secondary mt-1.5 px-2">
                            Unités initiales disponibles en réserve.
                        </p>
                    </div>

                    <div>
                        <label for="seuil_minimum" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Seuil minimum d'alerte <span class="text-content-secondary font-normal text-[11px] normal-case">(défaut : 1)</span>
                        </label>
                        <input
                            type="number"
                            id="seuil_minimum"
                            name="seuil_minimum"
                            value="{{ old('seuil_minimum', 1) }}"
                            min="0"
                            step="1"
                            placeholder="1"
                            class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs font-bold text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                        />
                        <p class="text-[11px] text-content-secondary mt-1.5 px-2">
                            Alerte déclenchée si les unités en stock tombent sous ce seuil.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Boutons -->
            <div class="p-4 sm:p-5 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                <a
                    href="{{ route('it.products.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-surface hover:bg-slate-100 rounded-full text-xs font-semibold text-content transition-colors"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-bold rounded-full shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors cursor-pointer"
                >
                    {!! $svg('check', 'w-4 h-4') !!}
                    Enregistrer le matériel
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('photo');
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');

            if (!input || !preview || !placeholder) return;

            input.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];

                if (file) {
                    // Vérification taille (2 Mo max) côté client (feedback immédiat)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('L\'image dépasse la taille maximale de 2 Mo.');
                        input.value = '';
                        preview.src = '';
                        preview.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        preview.src = ev.target.result;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            });
        });
    </script>

</x-layouts.dashboard>