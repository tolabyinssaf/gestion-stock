<x-layouts.dashboard title="Modifier la demande" :breadcrumbs="['Demandes de matériel' => route('it.demandes.index'), 'Modifier '.$demande->reference => null]">

    <div
        class="max-w-5xl mx-auto"
        x-data="{
            produitId: '{{ old('produit_id', $demande->produit_id) }}',
            quantite: {{ old('quantite', $demande->quantite) }},
            priorite: '{{ old('priorite', $demande->priorite) }}',
            prixEstime: '{{ old('prix_estime', $demande->prix_estime ?? '') }}',
            motif: '{{ addslashes(old('motif', $demande->motif)) }}',
            commentaire: '{{ addslashes(old('commentaire', $demande->commentaire ?? '')) }}',
            directeurNom: '{{ addslashes(old('destinataire_nom', $demande->destinataire_nom ?: ($directeurConfig['nom'] ?? 'Direction Générale'))) }}',
            directeurEmail: '{{ addslashes(old('email_destinataire', $demande->email_destinataire ?: ($directeurConfig['email'] ?? ''))) }}',
            isConfigured: {{ $isConfigured ? 'true' : 'false' }},
            userName: '{{ addslashes(Auth::user()->name) }}',
            userEmail: '{{ addslashes(old('email_expediteur', Auth::user()->email)) }}',
            productsMap: {
                @foreach ($produits as $p)
                    '{{ $p->id }}': {
                        designation: '{{ addslashes($p->designation ?? $p->nom) }}',
                        reference: '{{ addslashes($p->reference) }}',
                        marque: '{{ addslashes($p->marque ?? '') }}',
                        modele: '{{ addslashes($p->modele ?? '') }}',
                        categorie: '{{ addslashes($p->category->nom ?? ($p->categorie->nom ?? 'Non classé')) }}',
                        prixIndicatif: {{ $p->prix_indicatif !== null ? $p->prix_indicatif : 'null' }},
                        totalStock: {{ $p->total_stock }},
                        enStock: {{ $p->quantite_en_stock }},
                        enUtil: {{ $p->quantite_en_utilisation }},
                        enPanne: {{ $p->quantite_en_panne }},
                        seuilMin: {{ $p->seuil_minimum ?? 0 }},
                        stockStatus: '{{ $p->stock_status }}'
                    },
                @endforeach
            },
            getSelectedProduct() {
                return this.produitId && this.productsMap[this.produitId] ? this.productsMap[this.produitId] : null;
            },
            onProductChange() {
                const p = this.getSelectedProduct();
                if (p && p.prixIndicatif !== null && !this.prixEstime) {
                    this.prixEstime = p.prixIndicatif;
                }
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
                const enStock = p ? p.enStock : 0;
                const enUtil = p ? p.enUtil : 0;
                const enPanne = p ? p.enPanne : 0;
                const total = p ? p.totalStock : 0;
                const seuil = p ? p.seuilMin : 0;

                let prioriteTxt = 'Normale';
                if (this.priorite === 'importante') prioriteTxt = 'Importante';
                if (this.priorite === 'urgente') prioriteTxt = 'Urgente';

                const destNom = this.directeurNom ? this.directeurNom.trim() : 'Direction Générale';
                const destEmail = this.directeurEmail ? this.directeurEmail.trim() : '[Non renseigné]';

                let lines = [
                    'Bonjour ' + destNom + ',',
                    '',
                    'Je vous soumets une demande de matériel informatique :',
                    '',
                    '■ MATÉRIEL DEMANDÉ :',
                    '- Produit : ' + designation,
                    '- Référence : ' + ref,
                    '- Catégorie : ' + cat,
                    '- Quantité demandée : ' + this.quantite + ' unité(s)',
                    '- Priorité : ' + prioriteTxt,
                ];

                if (this.prixEstime && parseFloat(this.prixEstime) > 0) {
                    const pe = parseFloat(this.prixEstime);
                    lines.push('- Estimation du prix unitaire : ' + pe.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' € HT');
                    if (this.quantite > 1) {
                        const totalPe = pe * this.quantite;
                        lines.push('- Montant total estimé : ' + totalPe.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' € HT');
                    }
                }

                lines.push('');
                lines.push('■ SITUATION DU STOCK ACTUEL :');
                lines.push('- En stock : ' + enStock + ' unité(s)');
                lines.push('- En utilisation : ' + enUtil + ' unité(s)');
                lines.push('- En panne : ' + enPanne + ' unité(s)');
                lines.push('- Total : ' + total + ' unité(s)');

                if (seuil > 0) {
                    lines.push('- Seuil d\'alerte : ' + seuil + ' unité(s)');
                }

                if (this.motif && this.motif.trim().length > 0) {
                    lines.push('');
                    lines.push('■ MOTIF DE LA DEMANDE :');
                    lines.push(this.motif.trim());
                }

                if (this.commentaire && this.commentaire.trim().length > 0) {
                    lines.push('');
                    lines.push('■ OBSERVATIONS :');
                    lines.push(this.commentaire.trim());
                }

                lines.push('');
                lines.push('Je vous remercie de bien vouloir étudier cette demande.');
                lines.push('');
                lines.push('Cordialement,');
                lines.push(this.userName);

                return lines.join('\n');
            }
        }"
    >
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="edit" class="w-6 h-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-xl font-bold text-content tracking-tight">Modifier le brouillon de demande</h2>
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-surface border border-border text-primary">
                            {{ $demande->reference }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1">
                        Ajustez les éléments de la demande avant de finaliser la préparation du message Gmail.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('it.demandes.show', $demande) }}"
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

        <form method="POST" action="{{ route('it.demandes.update', $demande) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <!-- LEFT: Saisie -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Carte Produit & Quantité -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-7">
                        <div class="border-b border-border pb-4 mb-5">
                            <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                                <x-ui.icon name="box" class="w-4 h-4 text-primary" />
                                Matériel requis & Quantité
                            </h3>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label for="produit_id" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Produit du catalogue <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="produit_id"
                                    name="produit_id"
                                    x-model="produitId"
                                    @change="onProductChange()"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                >
                                    <option value="">Sélectionner un produit...</option>
                                    @foreach ($produits as $p)
                                        <option value="{{ $p->id }}" {{ (string)$p->id === (string)$demande->produit_id ? 'selected' : '' }}>
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

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                                        <option value="importante">Importante</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="prix_estime" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                        Estimation prix unitaire <span class="text-[10px] text-content-secondary font-normal lowercase">(facultatif)</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            id="prix_estime"
                                            name="prix_estime"
                                            x-model="prixEstime"
                                            placeholder="0.00"
                                            class="w-full py-2.5 pl-3.5 pr-8 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-semibold"
                                        >
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-content-secondary text-xs font-bold">
                                            €
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte Justification -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-7">
                        <div class="border-b border-border pb-4 mb-5">
                            <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1 flex items-center gap-2">
                                <x-ui.icon name="file-text" class="w-4 h-4 text-primary" />
                                Justification de la demande
                            </h3>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label for="motif" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Motif de la demande <span class="text-[10px] text-content-secondary font-normal lowercase">(facultatif)</span>
                                </label>
                                <input
                                    type="text"
                                    id="motif"
                                    name="motif"
                                    x-model="motif"
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                >
                            </div>

                            <div>
                                <label for="commentaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                                    Observations / Informations complémentaires
                                </label>
                                <textarea
                                    id="commentaire"
                                    name="commentaire"
                                    x-model="commentaire"
                                    rows="3"
                                    class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Résumé -->
                <div class="space-y-6">
                    <!-- Identification de l'e-mail (Expéditeur & Destinataire) -->
                    <div class="bg-white rounded-2xl border border-border shadow-xs p-6 space-y-5">
                        <div class="border-b border-border pb-3">
                            <h4 class="text-xs font-bold text-content uppercase tracking-wider flex items-center gap-2">
                                <x-ui.icon name="mail" class="w-4 h-4 text-primary" />
                                Identification de l'e-mail (De ... À ...)
                            </h4>
                            <p class="text-[11px] text-content-secondary mt-1">
                                Identifie précisément qui transmet la demande et qui en est le destinataire.
                            </p>
                        </div>

                        <!-- 1. DE : Expéditeur (Qui envoie) -->
                        <div class="p-3.5 bg-surface/70 border border-border rounded-xl space-y-2">
                            <span class="text-[10px] font-extrabold text-primary uppercase tracking-wider block">
                                📤 De (Expéditeur qui envoie)
                            </span>
                            <div class="text-xs font-bold text-content flex items-center justify-between">
                                <span>{{ Auth::user()->name }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-primary/10 text-primary font-semibold">Gestionnaire de stock</span>
                            </div>
                            <div>
                                <label for="email_expediteur" class="block text-[11px] font-bold text-content uppercase tracking-wider mb-1">
                                    Adresse e-mail de l'expéditeur
                                </label>
                                <input
                                    type="email"
                                    id="email_expediteur"
                                    name="email_expediteur"
                                    x-model="userEmail"
                                    placeholder="ex: expediteur@votre-entreprise.com"
                                    class="w-full px-3 py-2 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono"
                                >
                            </div>
                        </div>

                        <!-- 2. À : Destinataire (Qui reçoit) -->
                        <div class="space-y-3">
                            <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider block">
                                📥 À (Destinataire qui reçoit)
                            </span>

                            <div>
                                <label for="destinataire_nom" class="block text-[11px] font-bold text-content uppercase tracking-wider mb-1">
                                    Nom ou Service destinataire
                                </label>
                                <input
                                    type="text"
                                    id="destinataire_nom"
                                    name="destinataire_nom"
                                    x-model="directeurNom"
                                    class="w-full px-3 py-2 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-semibold"
                                >
                            </div>

                            <div>
                                <label for="email_destinataire" class="block text-[11px] font-bold text-content uppercase tracking-wider mb-1">
                                    Adresse e-mail du destinataire <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email_destinataire"
                                    name="email_destinataire"
                                    x-model="directeurEmail"
                                    required
                                    class="w-full px-3 py-2 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono"
                                >
                                @error('email_destinataire')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- APERÇU EN DIRECT -->
            <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8 mb-8">
                <div class="border-b border-border pb-4 mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider flex items-center gap-2">
                            <x-ui.icon name="mail-check" class="w-4 h-4 text-accent" />
                            Aperçu du message mis à jour
                        </h3>
                        <p class="text-xs text-content-secondary mt-0.5">Voici le texte exact qui sera injecté dans la fenêtre de composition de Gmail.</p>
                    </div>
                </div>

                <!-- En-tête De ... À ... visuel -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 p-3.5 bg-surface/80 rounded-xl border border-border text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-content-secondary uppercase block">Expéditeur (De) :</span>
                        <span class="font-semibold text-content">{{ Auth::user()->name }}</span>
                        <span class="text-content-secondary font-mono text-[11px]" x-text="'<' + (userEmail || 'Non spécifié') + '>'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-content-secondary uppercase block">Destinataire (À) :</span>
                        <span class="font-semibold text-content" x-text="directeurNom || 'Direction Générale'"></span>
                        <span class="text-content-secondary font-mono text-[11px]" x-text="'<' + (directeurEmail || 'Non spécifié') + '>'"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Objet</span>
                        <div class="p-3 bg-surface rounded-xl border border-border text-xs font-semibold text-content" x-text="getGeneratedSubject()"></div>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Corps</span>
                        <div class="p-4 bg-surface rounded-xl border border-border text-xs font-mono text-content whitespace-pre-wrap leading-relaxed" x-text="getGeneratedBody()"></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl border border-border shadow-xs p-5 flex items-center justify-between gap-4">
                <a
                    href="{{ route('it.demandes.show', $demande) }}"
                    class="px-5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    Annuler
                </a>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        name="action_type"
                        value="update"
                        class="px-5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors inline-flex items-center gap-2"
                    >
                        <x-ui.icon name="save" class="w-4 h-4 text-content-secondary" />
                        Mettre à jour le brouillon
                    </button>

                    <button
                        type="submit"
                        name="action_type"
                        value="prepare_gmail"
                        class="px-6 py-2.5 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-semibold shadow-xs transition-colors inline-flex items-center gap-2"
                    >
                        <x-ui.icon name="check" class="w-4 h-4" />
                        Finaliser & Rendre prête
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-layouts.dashboard>
