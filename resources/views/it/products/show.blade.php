<x-layouts.dashboard :title="$product->nom" :breadcrumbs="['Gestion du matériel' => route('it.products.index'), $product->nom => null]">

    <div
        x-data="{
            deleteModalOpen: false
        }"
        @keydown.escape.window="deleteModalOpen = false"
    >

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-emerald-800 text-sm">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between gap-3 text-red-800 text-sm">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Top Header & Action Bar -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a
                    href="{{ route('it.products.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-content-secondary hover:text-primary transition-colors mb-2"
                >
                    <x-ui.icon name="chevron-left" class="w-4 h-4" />
                    Retour à la gestion du matériel
                </a>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-content tracking-tight">{{ $product->nom }}</h1>

                    @if ($product->categorie)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-primary border border-blue-200">
                            <x-ui.icon name="layers" class="w-3.5 h-3.5" />
                            {{ $product->categorie->nom }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-content-secondary mt-1">
                    Référence : <code class="font-mono font-semibold text-content px-1.5 py-0.5 rounded bg-surface border border-border">{{ $product->reference }}</code>
                </p>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center flex-wrap">
                @if ($product->isActif())
                    <!-- Bouton Demander -->
                    <a
                        href="{{ route('it.demandes.create', ['produit_id' => $product->id]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors"
                        title="Préparer une demande de réapprovisionnement par Gmail"
                    >
                        <x-ui.icon name="mail" class="w-4 h-4" />
                        Demander du matériel
                    </a>
                @endif

                <!-- Edit button -->
                <a
                    href="{{ route('it.products.edit', $product) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="pencil" class="w-4 h-4" />
                    Modifier
                </a>

                <!-- Delete button -->
                <button
                    type="button"
                    @click="deleteModalOpen = true"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-xs font-semibold text-red-700 transition-colors"
                >
                    <x-ui.icon name="trash-2" class="w-4 h-4" />
                    Supprimer
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN: PHOTO & KEY DATA -->
            <div class="space-y-6">

                <!-- Photo Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs text-center">
                    <div class="w-full h-56 rounded-xl overflow-hidden bg-surface border border-border flex items-center justify-center relative mb-4">
                        @if ($product->photo_url)
                            <img
                                src="{{ $product->photo_url }}"
                                alt="{{ $product->nom }}"
                                class="w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            />
                            <div class="flex-col items-center justify-center text-content-secondary" style="display:none;">
                                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-2">
                                    <x-ui.icon :name="$product->categorie->icon_name ?? 'package'" class="w-8 h-8" />
                                </div>
                                <span class="text-xs text-content-secondary/80">Photo introuvable</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center text-content-secondary">
                                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-2">
                                    <x-ui.icon :name="$product->categorie->icon_name ?? 'package'" class="w-8 h-8" />
                                </div>
                                <span class="text-xs text-content-secondary/80">Aucune photo enregistrée</span>
                            </div>
                        @endif
                    </div>

                    <h2 class="text-base font-bold text-content">{{ $product->nom }}</h2>
                    <p class="text-xs text-content-secondary mt-0.5">
                        {{ $product->marque ?: 'Marque non spécifiée' }}
                        @if ($product->modele)
                            &bull; {{ $product->modele }}
                        @endif
                    </p>
                </div>

                <!-- Technical Attributes Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <h3 class="text-xs font-bold text-content uppercase tracking-wider mb-4 pb-3 border-b border-border">
                        Caractéristiques de référence
                    </h3>

                    <dl class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Catégorie</dt>
                            <dd>
                                <a
                                    href="{{ route('it.products.index', ['category_id' => $product->categorie_id]) }}"
                                    class="inline-flex items-center gap-1.5 font-semibold text-primary hover:underline"
                                >
                                    <x-ui.icon :name="$product->categorie->icon_name ?? 'layers'" class="w-3.5 h-3.5" />
                                    {{ $product->categorie->nom ?? 'Non classé' }}
                                </a>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Référence (SKU)</dt>
                            <dd class="font-mono font-semibold text-content">{{ $product->reference }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Marque</dt>
                            <dd class="font-medium text-content">{{ $product->marque ?: '—' }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Modèle</dt>
                            <dd class="font-medium text-content">{{ $product->modele ?: '—' }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Mode de suivi</dt>
                            <dd class="font-semibold text-content">{{ $product->type_suivi_label }}</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Prix unitaire indicatif</dt>
                            <dd class="font-bold text-content">
                                @if ($product->prix_indicatif !== null)
                                    {{ number_format($product->prix_indicatif, 2, ',', ' ') }} € HT
                                @else
                                    <span class="text-content-secondary">—</span>
                                @endif
                            </dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-content-secondary">Seuil d'alerte stock</dt>
                            <dd class="font-bold text-accent">
                                {{ $product->seuil_minimum }} unité(s)
                            </dd>
                        </div>

                        <div class="pt-3 border-t border-border flex items-center justify-between text-[11px]">
                            <dt class="text-content-secondary">Créé le</dt>
                            <dd class="text-content-secondary">{{ $product->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <dt class="text-content-secondary">Dernière modification</dt>
                            <dd class="text-content-secondary">{{ $product->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>

            </div>

            <!-- RIGHT COLUMN: DESCRIPTIONS & INVENTORY DECOUPLING -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Description Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-border">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <x-ui.icon name="file-text" class="w-4 h-4" />
                        </div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                            Description générale
                        </h3>
                    </div>

                    @if ($product->description)
                        <div class="text-xs text-content leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    @else
                        <p class="text-xs text-content-secondary italic">Aucune description n'a été saisie pour ce modèle.</p>
                    @endif
                </div>

                <!-- Technical Characteristics Card -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-border">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-primary-light flex items-center justify-center">
                            <x-ui.icon name="cpu" class="w-4 h-4" />
                        </div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                            Spécifications techniques
                        </h3>
                    </div>

                    @if ($product->caracteristiques)
                        <pre class="text-xs font-mono text-content bg-surface p-4 rounded-xl border border-border leading-relaxed whitespace-pre-wrap">{{ $product->caracteristiques }}</pre>
                    @else
                        <p class="text-xs text-content-secondary italic">Aucune caractéristique technique détaillée n'a été spécifiée.</p>
                    @endif
                </div>

                <!-- Situation actuelle du matériel -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <x-ui.icon name="boxes" class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                                    Situation & Quantités du matériel
                                </h3>
                                <p class="text-[11px] text-content-secondary">Répartition entre Réserve, Utilisation et Panne</p>
                            </div>
                        </div>

                        <a
                            href="{{ route('it.stocks.index', ['search' => $product->nom]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border text-xs font-semibold text-primary hover:bg-surface transition-colors"
                        >
                            <x-ui.icon name="refresh-cw" class="w-3.5 h-3.5" />
                            Gérer le stock
                        </a>
                    </div>

                    <!-- 3 Cartes de situation + Total -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200">
                            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">En stock</span>
                            <span class="text-xl font-extrabold text-emerald-700 block mt-0.5">{{ $product->quantite_en_stock }}</span>
                            <span class="text-[10px] text-emerald-600">Disponible en réserve</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200">
                            <span class="text-[11px] font-bold text-primary uppercase tracking-wider block">En utilisation</span>
                            <span class="text-xl font-extrabold text-primary block mt-0.5">{{ $product->quantite_en_utilisation }}</span>
                            <span class="text-[10px] text-primary/70">En production</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-rose-50/70 border border-rose-200">
                            <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider block">En panne</span>
                            <span class="text-xl font-extrabold text-rose-600 block mt-0.5">{{ $product->quantite_en_panne }}</span>
                            <span class="text-[10px] text-rose-600">Hors service</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-surface border border-border">
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block">Total global</span>
                            <span class="text-xl font-extrabold text-content block mt-0.5">{{ $product->total_stock }}</span>
                            <span class="text-[10px] text-content-secondary">Total des unités</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Delete Confirmation Modal -->
        <div
            x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>

            <div class="relative w-full max-w-md mx-auto my-12 bg-white rounded-2xl border border-border shadow-2xl p-6 overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="trash-2" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-content">Supprimer la référence</h3>
                        <p class="text-xs text-content-secondary mt-1">
                            Êtes-vous sûr de vouloir supprimer définitivement le produit
                            <strong class="text-content">« {{ $product->nom }} »</strong> (SKU: {{ $product->reference }}) ?
                        </p>
                        <p class="text-[11px] text-content-secondary mt-2">
                            Cette action supprimera définitivement le produit ainsi que le visuel associé.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="deleteModalOpen = false"
                        class="px-4 py-2 border border-border rounded-xl text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        Annuler
                    </button>
                    <form action="{{ route('it.products.destroy', $product) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors"
                        >
                            Confirmer la suppression
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-layouts.dashboard>
