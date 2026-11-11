<x-layouts.dashboard title="Nouvelle demande de matériel" :breadcrumbs="['Demandes de matériel' => route('it.demandes.index'), 'Nouvelle demande' => null]">

    <div
        class="max-w-5xl mx-auto"
        x-data="{
            produitId: '{{ old('produit_id', $selectedProduit->id ?? '') }}',
            quantite: {{ old('quantite', 1) }},
            priorite: '{{ old('priorite', 'normale') }}',
            motif: '{{ addslashes(old('motif', '')) }}',
            commentaire: '{{ addslashes(old('commentaire', '')) }}',
            directeurNom: '{{ addslashes($directeurConfig['nom'] ?? 'Direction Générale') }}',
            directeurEmail: '{{ addslashes($directeurConfig['email'] ?? '') }}',
            isConfigured: {{ $isConfigured ? 'true' : 'false' }},
            userName: '{{ addslashes(Auth::user()->name) }}',
            productsMap: {
                @foreach ($produits as $p)
                    '{{ $p->id }}': {
                        designation: '{{ addslashes($p->designation ?? $p->nom) }}',
                        reference: '{{ addslashes($p->reference) }}',
                        marque: '{{ addslashes($p->marque ?? '') }}',
                        modele: '{{ addslashes($p->modele ?? '') }}',
                        categorie: '{{ addslashes($p->category->nom ?? ($p->categorie->nom ?? 'Non classé')) }}',
                        typeSuivi: '{{ $p->type_suivi }}',
                        totalStock: {{ $p->total_stock }},
                        seuilMin: {{ $p->seuil_minimum ?? 0 }},
                        stockStatus: '{{ $p->stock_status }}',
                        photoUrl: '{{ $p->photo_url ?? '' }}'
                    },
                @endforeach
            },
            getSelectedProduct() {
                return this.produitId && this.productsMap[this.produitId] ? this.productsMap[this.produitId] : null;
            },
            getGeneratedSubject() {
                const p = this.getSelectedProduct();
                if (!p) return 'Demande de matériel informatique';
                return 'Demande de matériel informatique — ' + p.designation;
            },
            getGeneratedBody() {
                const p = this.getSelectedProduct();
                const designation = p ? p.designation : '[Produit non sélectionné]';
                const ref = p ? p.reference : '-';
                const cat = p ? p.categorie : '-';
                const stock = p ? p.totalStock : 0;
                const seuil = p ? p.seuilMin : 0;

                let prioriteTxt = 'Normale';
                if (this.priorite === 'importante') prioriteTxt = 'Importante';
                if (this.priorite === 'urgente') prioriteTxt = 'Urgente';

                let lines = [
                    'Bonjour,',
                    '',
                    'Je souhaite vous soumettre une demande de matériel informatique pour les besoins de l\'entreprise.',
                    '',
                    'Matériel demandé :',
                    '- Produit : ' + designation,
                    '- Référence : ' + ref,
                    '- Catégorie : ' + cat,
                    '- Quantité demandée : ' + this.quantite,
                    '- Priorité : ' + prioriteTxt,
                    '',
                    'Situation actuelle :',
                    '- Disponibilité en stock : ' + stock + (stock > 1 ? ' unités' : ' unité')
                ];

                if (seuil > 0) {
                    lines.push('- Seuil d\'alerte : ' + seuil + ' unités');
                }

                lines.push('');
                lines.push('Motif de la demande :');
                lines.push(this.motif ? this.motif.trim() : '[Motif à renseigner]');

                if (this.commentaire && this.commentaire.trim().length > 0) {
                    lines.push('');
                    lines.push('Observations complémentaires :');
                    lines.push(this.commentaire.trim());
                }

                lines.push('');
                lines.push('Je vous remercie de bien vouloir étudier cette demande.');
                lines.push('');
                lines.push('Cordialement,');
                lines.push('');
                lines.push(this.userName);
                lines.push('Service informatique — IT Manager Pro');

                return lines.join('\n');
            }
        }"
    >
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="file-plus" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Préparer une demande de matériel</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Formulez votre besoin, vérifiez le stock disponible et générez un message prérempli à transmettre via Gmail à la Direction.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('it.demandes.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
            >
                <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                Historique des demandes
            </a>
        </div>

        @if (!$isConfigured)
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-start gap-3">
                <x-ui.icon name="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold">Configuration requise :</span>
                    L'adresse e-mail de la Direction n'a pas encore été configurée dans l'application. Vous pouvez enregistrer un brouillon, mais l'ouverture directe dans Gmail nécessite que l'administrateur renseigne l'adresse dans
                    <span class="font-semibold underline">Paramètres &rarr; Destinataire Direction</span>.
                </div>
            </div>
        @endif

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

        <form method="POST" action="{{ route('it.demandes.store') }}" id="demandeForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <!-- LEFT: Formulaire de saisie -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Carte 1: Choix du produit & Quantité -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-7">
                        <div class="border-b border-border pb-4 mb-5">
                            <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                                <x-ui.icon name="box" class="w-4 h-4 text-accent" />
                                Matériel requis & Quantité
                            </h3>
                            <p class="text-xs text-content-secondary">Sélectionnez le modèle informatique concerné et le nombre d'unités souhaitées.</p>
                        </div>

                        <div class="space-y-5">
                            <!-- Produit -->
                            <div>
                                <label for="produit_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Produit du catalogue <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="produit_id"
                                    name="produit_id"
                                    x-model="produitId"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                >
                                    <option value="">Sélectionner un produit...</option>
                                    @foreach ($produits as $p)
                                        <option value="{{ $p->id }}" {{ (string)$p->id === (string)($selectedProduit->id ?? '') ? 'selected' : '' }}>
                                            [{{ $p->reference }}] {{ $p->designation ?? $p->nom }} &mdash; (Dispo: {{ $p->total_stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantité actuellement en stock (lecture seule) -->
                            <div>
                                <label class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Quantité actuellement en stock <span class="text-[10px] text-content-secondary font-normal lowercase">(lecture seule)</span>
                                </label>
                                <div class="px-3.5 py-2.5 bg-surface/70 border border-border rounded-xl text-xs font-bold flex items-center justify-between">
                                    <span x-text="getSelectedProduct() ? getSelectedProduct().totalStock + ' unité(s) en stock' : 'Veuillez sélectionner un matériel ci-dessus'"></span>
                                    <template x-if="getSelectedProduct()">
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                            :class="{
                                                'bg-rose-100 text-rose-700 border border-rose-200': getSelectedProduct().totalStock === 0,
                                                'bg-amber-100 text-amber-800 border border-amber-200': getSelectedProduct().totalStock === 1,
                                                'bg-emerald-100 text-emerald-700 border border-emerald-200': getSelectedProduct().totalStock > 1
                                            }"
                                            x-text="getSelectedProduct().totalStock === 0 ? 'Rupture (0)' : (getSelectedProduct().totalStock === 1 ? 'Stock faible (1)' : 'Disponible (> 1)')"
                                        ></span>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Quantité demandée -->
                                <div>
                                    <label for="quantite" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                        Quantité demandée <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="quantite"
                                        name="quantite"
                                        min="1"
                                        x-model.number="quantite"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-bold"
                                    >
                                </div>

                                <!-- Priorité -->
                                <div>
                                    <label for="priorite" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                        Niveau de priorité <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="priorite"
                                        name="priorite"
                                        x-model="priorite"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                    >
                                        <option value="normale">Normale</option>
                                        <option value="urgente">Urgente</option>
                                        <option value="importante">Importante</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte 2: Justification & Commentaire -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-7">
                        <div class="border-b border-border pb-4 mb-5">
                            <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                                <x-ui.icon name="file-text" class="w-4 h-4 text-accent" />
                                Justification de la demande
                            </h3>
                            <p class="text-xs text-content-secondary">Expliquez le contexte de la demande pour la Direction Générale.</p>
                        </div>

                        <div class="space-y-5">
                            <!-- Motif -->
                            <div>
                                <label for="motif" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Motif de la demande <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="motif"
                                    name="motif"
                                    x-model="motif"
                                    required
                                    placeholder="ex: Dotation pour les 2 nouveaux collaborateurs prévus le mois prochain..."
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                >
                            </div>

                            <!-- Commentaire facultatif -->
                            <div>
                                <label for="commentaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Observations / Informations complémentaires
                                </label>
                                <textarea
                                    id="commentaire"
                                    name="commentaire"
                                    x-model="commentaire"
                                    rows="3"
                                    placeholder="Précisions sur les délais, contraintes techniques, service demandeur..."
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Résumé du produit, Stock & Destinataire -->
                <div class="space-y-6">

                    <!-- Résumé du produit sélectionné -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6">
                        <h4 class="text-xs font-bold text-content uppercase tracking-wider mb-4 flex items-center gap-2">
                            <x-ui.icon name="info" class="w-4 h-4 text-primary" />
                            Matériel sélectionné
                        </h4>

                        <template x-if="getSelectedProduct()">
                            <div class="space-y-4">
                                <div class="p-3.5 rounded-xl bg-surface border border-border">
                                    <div class="text-xs font-bold text-content" x-text="getSelectedProduct().designation"></div>
                                    <div class="text-[11px] text-content-secondary mt-1">
                                        Réf: <span class="font-mono font-semibold text-content" x-text="getSelectedProduct().reference"></span>
                                    </div>
                                    <div class="text-[11px] text-content-secondary mt-0.5">
                                        Catégorie: <span class="font-medium text-content" x-text="getSelectedProduct().categorie"></span>
                                    </div>
                                </div>

                                <!-- Situation du stock -->
                                <div>
                                    <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-2">Situation du stock</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="p-2.5 rounded-lg border border-border bg-white text-center">
                                            <span class="block text-[10px] text-content-secondary uppercase">Dispo en stock</span>
                                            <span class="text-base font-bold text-content" x-text="getSelectedProduct().totalStock"></span>
                                        </div>
                                        <div class="p-2.5 rounded-lg border border-border bg-white text-center">
                                            <span class="block text-[10px] text-content-secondary uppercase">Seuil d'alerte</span>
                                            <span class="text-base font-bold text-content-secondary" x-text="getSelectedProduct().seuilMin"></span>
                                        </div>
                                    </div>

                                    <div class="mt-2.5">
                                        <template x-if="getSelectedProduct().stockStatus === 'rupture'">
                                            <div class="p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-2">
                                                <x-ui.icon name="alert-circle" class="w-4 h-4 text-rose-600" />
                                                Article en rupture totale de stock
                                            </div>
                                        </template>
                                        <template x-if="getSelectedProduct().stockStatus === 'faible'">
                                            <div class="p-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-[11px] font-semibold flex items-center gap-2">
                                                <x-ui.icon name="alert-triangle" class="w-4 h-4 text-amber-600" />
                                                Stock sous le seuil d'alerte minimum
                                            </div>
                                        </template>
                                        <template x-if="getSelectedProduct().stockStatus === 'normal'">
                                            <div class="p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-semibold flex items-center gap-2">
                                                <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-600" />
                                                Stock théorique suffisant
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!getSelectedProduct()">
                            <div class="p-6 text-center text-xs text-content-secondary italic bg-surface/50 rounded-xl border border-dashed border-border">
                                Aucun produit sélectionné pour l'instant.
                            </div>
                        </template>
                    </div>

                    <!-- Destinataire -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6">
                        <h4 class="text-xs font-bold text-content uppercase tracking-wider mb-3 flex items-center gap-2">
                            <x-ui.icon name="mail" class="w-4 h-4 text-primary" />
                            Destinataire de l'e-mail
                        </h4>
                        <div class="text-xs text-content space-y-1">
                            <div class="font-semibold text-content" x-text="directeurNom"></div>
                            <div class="font-mono text-[11px] text-content-secondary" x-text="directeurEmail || 'Non configuré'"></div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- SECTION: APERÇU DU MESSAGE GMAIL EN TEMPS RÉEL -->
            <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8 mb-8">
                <div class="border-b border-border pb-4 mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                            <x-ui.icon name="mail-check" class="w-4 h-4 text-accent" />
                            Aperçu du message préparé
                        </h3>
                        <p class="text-xs text-content-secondary mt-0.5">Voici le texte exact qui sera injecté dans la fenêtre de composition de Gmail.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Objet -->
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Objet du message</span>
                        <div class="p-3 bg-surface rounded-xl border border-border text-xs font-semibold text-content" x-text="getGeneratedSubject()"></div>
                    </div>

                    <!-- Corps -->
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Corps du message</span>
                        <div class="p-4 bg-surface rounded-xl border border-border text-xs font-mono text-content whitespace-pre-wrap leading-relaxed" x-text="getGeneratedBody()"></div>
                    </div>
                </div>
            </div>

            <!-- Actions de soumission -->
            <div class="bg-white rounded-2xl border border-border shadow-xs p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <a
                    href="{{ route('it.demandes.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors text-center"
                >
                    Annuler
                </a>

                <div class="flex items-center gap-3 justify-end flex-wrap">
                    <!-- Bouton 1: Enregistrer brouillon -->
                    <button
                        type="submit"
                        name="action_type"
                        value="save_draft"
                        class="px-5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors inline-flex items-center gap-2"
                    >
                        <x-ui.icon name="save" class="w-4 h-4 text-content-secondary" />
                        Enregistrer en brouillon
                    </button>

                    <!-- Bouton 2: Préparer & Ouvrir Gmail -->
                    <button
                        type="submit"
                        name="action_type"
                        value="prepare_gmail"
                        :disabled="!isConfigured"
                        class="px-6 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        :title="!isConfigured ? 'Veuillez configurer l\'adresse de la Direction dans les paramètres' : ''"
                    >
                        <x-ui.icon name="send" class="w-4 h-4" />
                        Préparer & Valider la demande
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-layouts.dashboard>
