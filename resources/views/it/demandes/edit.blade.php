@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'edit'           => 'M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z|m15 5 4 4',
        'file-text'      => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M10 9H8|M16 13H8|M16 17H8',
        'arrow-left'     => 'm12 19-7-7 7-7|M19 12H5',
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'box'            => 'M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
        'mail'           => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'mail-check'     => 'M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h8|m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7|m16 19 2 2 4-4',
        'save'           => 'M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7|M7 3v4a1 1 0 0 0 1 1h7',
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

<x-layouts.dashboard title="Modifier la demande" :breadcrumbs="['Demandes de matériel' => route('it.demandes.index'), 'Modifier '.$demande->reference => null]">

    <div
        class="max-w-6xl mx-auto"
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
        <!-- En-tête -->
        <div class="mb-6">
            <a
                href="{{ route('it.demandes.show', $demande) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white text-content text-xs font-semibold shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] hover:text-[#0092FD] transition-all mb-4"
            >
                {!! $svg('arrow-left', 'w-4 h-4') !!}
                Retour
            </a>

            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('edit', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-content tracking-tight">Modifier le brouillon de demande</h1>
                        <span class="font-mono text-xs font-bold px-3 py-1 rounded-full bg-[#0092FD]/10 text-[#0092FD]">
                            {{ $demande->reference }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-content-secondary mt-0.5">
                        Ajustez les éléments de la demande avant de finaliser la préparation du message Gmail.
                    </p>
                </div>
            </div>
        </div>

        <!-- Erreurs de validation -->
        @if ($errors->any())
            <div class="mb-5 p-4 bg-rose-50 rounded-2xl text-rose-800 text-xs">
                <div class="font-bold mb-1.5 flex items-center gap-2">
                    {!! $svg('alert-triangle', 'w-4 h-4 text-rose-600') !!}
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-5 sm:mb-6">

                <!-- GAUCHE : Saisie -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">

                    <!-- Carte Produit & Quantité -->
                    <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-7">
                        <div class="flex items-center gap-3 pb-4 mb-5 border-b border-border/60">
                            <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                {!! $svg('box', 'w-5 h-5') !!}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">Matériel requis & Quantité</h3>
                                <p class="text-xs text-content-secondary mt-0.5">Sélectionnez le modèle informatique concerné et le nombre d'unités souhaitées.</p>
                            </div>
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
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
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
                                <div class="px-4 py-2.5 bg-surface/70 rounded-2xl text-xs font-bold flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <span x-text="getSelectedProduct() ? getSelectedProduct().totalStock + ' unité(s) en stock' : 'Veuillez sélectionner un matériel ci-dessus'"></span>
                                    <template x-if="getSelectedProduct()">
                                        <span
                                            class="self-start sm:self-auto px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                            :class="{
                                                'bg-rose-50 text-rose-700': getSelectedProduct().totalStock === 0,
                                                'bg-amber-50 text-amber-700': getSelectedProduct().totalStock === 1,
                                                'bg-emerald-50 text-emerald-700': getSelectedProduct().totalStock > 1
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
                                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all font-bold"
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
                                        class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
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
                                            class="w-full py-2.5 pl-4 pr-9 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all font-semibold"
                                        >
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-content-secondary text-xs font-bold">
                                            €
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte Justification -->
                    <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-7">
                        <div class="flex items-center gap-3 pb-4 mb-5 border-b border-border/60">
                            <div class="w-10 h-10 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                                {!! $svg('file-text', 'w-5 h-5') !!}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-content uppercase tracking-wider">Justification de la demande</h3>
                                <p class="text-xs text-content-secondary mt-0.5">Expliquez le contexte de la demande pour la Direction Générale.</p>
                            </div>
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
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
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
                                    class="w-full px-4 py-3 bg-surface/60 border border-transparent rounded-2xl text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- DROITE : Identification de l'e-mail -->
                <div class="space-y-4 sm:space-y-6">
                    <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-6 space-y-5">
                        <div class="pb-4 border-b border-border/60">
                            <h4 class="text-xs font-bold text-content uppercase tracking-wider flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                                    {!! $svg('mail', 'w-4 h-4') !!}
                                </span>
                                Identification de l'e-mail (De ... À ...)
                            </h4>
                            <p class="text-[11px] text-content-secondary mt-2">
                                Identifie précisément qui transmet la demande et qui en est le destinataire.
                            </p>
                        </div>

                        <!-- 1. DE : Expéditeur (Qui envoie) -->
                        <div class="p-4 bg-surface/70 rounded-2xl space-y-2.5">
                            <span class="text-[10px] font-extrabold text-[#0092FD] uppercase tracking-wider block">
                                📤 De (Expéditeur qui envoie)
                            </span>
                            <div class="text-xs font-bold text-content flex flex-wrap items-center justify-between gap-2">
                                <span class="break-words">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] px-2.5 py-1 rounded-full bg-[#0092FD]/10 text-[#0092FD] font-semibold">Gestionnaire de stock</span>
                            </div>
                            <div>
                                <label for="email_expediteur" class="block text-[11px] font-bold text-content uppercase tracking-wider mb-1.5">
                                    Adresse e-mail de l'expéditeur
                                </label>
                                <input
                                    type="email"
                                    id="email_expediteur"
                                    name="email_expediteur"
                                    x-model="userEmail"
                                    placeholder="ex: expediteur@votre-entreprise.com"
                                    class="w-full px-4 py-2.5 bg-white border border-transparent rounded-full text-xs text-content focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all font-mono"
                                >
                            </div>
                        </div>

                        <!-- 2. À : Destinataire (Qui reçoit) -->
                        <div class="space-y-3">
                            <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider block">
                                📥 À (Destinataire qui reçoit)
                            </span>

                            <div>
                                <label for="destinataire_nom" class="block text-[11px] font-bold text-content uppercase tracking-wider mb-1.5">
                                    Nom ou Service destinataire
                                </label>
                                <input
                                    type="text"
                                    id="destinataire_nom"
                                    name="destinataire_nom"
                                    x-model="directeurNom"
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all font-semibold"
                                >
                            </div>

                            <div>
                                <label for="email_destinataire" class="block text-[11px] font-bold text-content uppercase tracking-wider mb-1.5">
                                    Adresse e-mail du destinataire <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email_destinataire"
                                    name="email_destinataire"
                                    x-model="directeurEmail"
                                    required
                                    class="w-full px-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all font-mono"
                                >
                                @error('email_destinataire')
                                    <p class="text-[11px] text-rose-600 mt-1.5 px-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- APERÇU EN DIRECT -->
            <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-8 mb-5 sm:mb-6">
                <div class="flex items-center gap-3 pb-4 mb-4 border-b border-border/60">
                    <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                        {!! $svg('mail-check', 'w-5 h-5') !!}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-content uppercase tracking-wider">Aperçu du message mis à jour</h3>
                        <p class="text-xs text-content-secondary mt-0.5">Voici le texte exact qui sera injecté dans la fenêtre de composition de Gmail.</p>
                    </div>
                </div>

                <!-- En-tête De ... À ... visuel -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 p-4 bg-surface/70 rounded-2xl text-xs">
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold text-content-secondary uppercase block">Expéditeur (De) :</span>
                        <span class="font-semibold text-content">{{ Auth::user()->name }}</span>
                        <span class="text-content-secondary font-mono text-[11px] break-all" x-text="'<' + (userEmail || 'Non spécifié') + '>'"></span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold text-content-secondary uppercase block">Destinataire (À) :</span>
                        <span class="font-semibold text-content" x-text="directeurNom || 'Direction Générale'"></span>
                        <span class="text-content-secondary font-mono text-[11px] break-all" x-text="'<' + (directeurEmail || 'Non spécifié') + '>'"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1.5">Objet</span>
                        <div class="p-3.5 bg-surface/70 rounded-2xl text-xs font-semibold text-content break-words" x-text="getGeneratedSubject()"></div>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1.5">Corps</span>
                        <div class="p-4 bg-surface/70 rounded-2xl text-xs font-mono text-content whitespace-pre-wrap break-words leading-relaxed" x-text="getGeneratedBody()"></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-4 sm:p-5 mb-6 flex flex-col-reverse lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
                <a
                    href="{{ route('it.demandes.show', $demande) }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-surface hover:bg-slate-100 text-xs font-semibold text-content transition-colors"
                >
                    Annuler
                </a>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3 lg:justify-end">
                    <button
                        type="submit"
                        name="action_type"
                        value="update"
                        class="px-5 py-2.5 rounded-full bg-white text-xs font-semibold text-content shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] transition-all inline-flex items-center justify-center gap-2 cursor-pointer"
                    >
                        {!! $svg('save', 'w-4 h-4 text-[#0092FD]') !!}
                        Mettre à jour le brouillon
                    </button>

                    <button
                        type="submit"
                        name="action_type"
                        value="prepare_gmail"
                        class="px-6 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors inline-flex items-center justify-center gap-2 cursor-pointer"
                    >
                        {!! $svg('check', 'w-4 h-4') !!}
                        Finaliser & Rendre prête
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-layouts.dashboard>