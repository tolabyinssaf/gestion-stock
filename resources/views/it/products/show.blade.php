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

                    <!-- Status Badge -->
                    @if ($product->isActif())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Actif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Archivé
                        </span>
                    @endif

                    <!-- Tracking Type Badge -->
                    @if ($product->isIndividuel())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-primary-light border border-blue-200">
                            <x-ui.icon name="tag" class="w-3.5 h-3.5" />
                            Sérialisé (N° de série individuel)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-accent border border-amber-200">
                            <x-ui.icon name="boxes" class="w-3.5 h-3.5" />
                            En quantité (volume de stock)
                        </span>
                    @endif
                </div>
                <p class="text-xs text-content-secondary mt-1">
                    Référence SKU : <code class="font-mono font-semibold text-content px-1.5 py-0.5 rounded bg-surface border border-border">{{ $product->reference }}</code>
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

                <!-- Toggle status button -->
                <form method="POST" action="{{ route('it.products.toggle-status', $product) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                        title="{{ $product->isActif() ? 'Archiver le produit' : 'Réactiver le produit' }}"
                    >
                        <x-ui.icon name="archive" class="w-4 h-4 text-content-secondary" />
                        {{ $product->isActif() ? 'Archiver' : 'Réactiver' }}
                    </button>
                </form>

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
                            />
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

                <!-- Decoupled Stock & Inventory Placeholder Section -->
                <div class="bg-white rounded-2xl border border-border p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center">
                                <x-ui.icon name="boxes" class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                                    Unités physiques & État des stocks
                                </h3>
                                <p class="text-[11px] text-content-secondary">Traçabilité opérationnelle du matériel</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-accent border border-amber-200">
                            Module 05 &mdash; Stocks & Mouvements
                        </span>
                    </div>

                    <div class="p-5 rounded-xl bg-surface/70 border border-border text-center">
                        <div class="w-12 h-12 rounded-xl bg-white mx-auto flex items-center justify-center text-accent mb-3 shadow-2xs border border-border">
                            <x-ui.icon name="boxes" class="w-6 h-6" />
                        </div>

                        <h4 class="text-sm font-bold text-content">Architecture découplée Catalogue & Stocks</h4>
                        <p class="text-xs text-content-secondary max-w-lg mx-auto mt-1 leading-relaxed">
                            Cette fiche produit définit les propriétés permanentes du matériel.
                            @if ($product->isIndividuel())
                                L'inventaire des unités physiques (numéros de série individuels, statut disponible / en utilisation / en panne, affectations aux collaborateurs) sera géré lors du déploiement du module Stocks & Mouvements.
                            @else
                                La quantité totale en stock, les entrées / sorties par lot et le calcul d'alerte en temps réel (seuil configuré à {{ $product->seuil_minimum }} unités) seront gérés lors du déploiement du module Stocks & Mouvements.
                            @endif
                        </p>

                        <div class="mt-4 flex items-center justify-center gap-4 text-xs font-semibold text-content-secondary">
                            <span class="inline-flex items-center gap-1.5">
                                <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-600" />
                                Modèle catalogué
                            </span>
                            <span class="text-border">&bull;</span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-ui.icon name="tag" class="w-4 h-4 text-primary" />
                                Règle : {{ $product->type_suivi_label }}
                            </span>
                            <span class="text-border">&bull;</span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-ui.icon name="alert-circle" class="w-4 h-4 text-accent" />
                                Seuil d'alerte : {{ $product->seuil_minimum }}
                            </span>
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
                            Cette action supprimera également le visuel associé. Si vous souhaitez simplement retirer ce modèle des nouvelles attributions, privilégiez plutôt l'archivage.
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
