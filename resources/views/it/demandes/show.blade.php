<x-layouts.dashboard title="Détail de la demande" :breadcrumbs="['Demandes de matériel' => route('it.demandes.index'), 'Fiche '.$demande->reference => null]">

    <div
        class="max-w-4xl mx-auto"
        x-data="{
            isOpened: {{ $demande->statut_preparation === \App\Models\DemandeMateriel::STATUT_OUVERTURE_GMAIL ? 'true' : 'false' }},
            gmailUrl: '{{ $gmailUrl }}',
            statusLabel: '{{ $demande->statut_preparation_label }}',
            dateOuverture: '{{ $demande->date_ouverture_gmail ? $demande->date_ouverture_gmail->format('d/m/Y à H:i') : '' }}',
            openGmail() {
                if (!this.gmailUrl) {
                    alert('L\'adresse e-mail de la direction n\'est pas configurée.');
                    return;
                }

                // Open Gmail in new tab
                const newWindow = window.open(this.gmailUrl, '_blank', 'noopener,noreferrer');

                // Record opening status asynchronously
                fetch('{{ route('it.demandes.gmail-opened', $demande) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.isOpened = true;
                        this.statusLabel = data.statut_label;
                        const now = new Date();
                        this.dateOuverture = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    }
                })
                .catch(err => console.error('Erreur enregistrement ouverture:', err));

                if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                    // Popup blocked fallback notification
                    document.getElementById('popupBlockedNotice').style.display = 'block';
                }
            }
        }"
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

        <!-- Popup Blocked Fallback Alert -->
        <div id="popupBlockedNotice" style="display: none;" class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-ui.icon name="alert-circle" class="w-4 h-4 text-amber-600" />
                    <span>Votre navigateur a bloqué l'ouverture automatique du nouvel onglet.</span>
                </div>
                <a :href="gmailUrl" target="_blank" rel="noopener noreferrer" class="px-3 py-1 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700">
                    Ouvrir Gmail manuellement &rarr;
                </a>
            </div>
        </div>

        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="file-text" class="w-6 h-6" />
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-xl font-bold text-content tracking-tight">Demande de matériel</h2>
                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-surface border border-border text-primary">
                            {{ $demande->reference }}
                        </span>

                        <!-- Badge Statut -->
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border"
                            :class="{
                                'bg-slate-100 text-slate-700 border-slate-200': statusLabel === 'Brouillon',
                                'bg-amber-50 text-amber-700 border-amber-200': statusLabel === 'Préparée',
                                'bg-blue-50 text-blue-700 border-blue-200': statusLabel === 'Ouverte dans Gmail',
                                'bg-emerald-50 text-emerald-700 border-emerald-200': statusLabel.includes('Envoyée')
                            }"
                        >
                            <span class="w-1.5 h-1.5 rounded-full" :class="{
                                'bg-slate-400': statusLabel === 'Brouillon',
                                'bg-amber-500': statusLabel === 'Préparée',
                                'bg-blue-500': statusLabel === 'Ouverte dans Gmail',
                                'bg-emerald-500': statusLabel.includes('Envoyée')
                            }"></span>
                            <span x-text="statusLabel"></span>
                        </span>
                    </div>
                    <p class="text-xs text-content-secondary mt-1">
                        Créée le {{ $demande->created_at->format('d/m/Y à H:i') }} par {{ $demande->user->name ?? 'Responsable IT' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a
                    href="{{ route('it.demandes.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                    Retour
                </a>

                @if (in_array($demande->statut_preparation, ['prete', 'ouverture_gmail']))
                    <form method="POST" action="{{ route('it.demandes.mark-sent', $demande) }}" class="inline" onsubmit="return confirm('Confirmez-vous avoir envoyé cet e-mail depuis votre messagerie Gmail ? Cette action est déclarative.');">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors"
                            title="Indiquer que l'e-mail a bien été envoyé dans Gmail"
                        >
                            <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-600" />
                            Marquer comme envoyée (déclaratif)
                        </button>
                    </form>
                @endif

                @if ($demande->statut_preparation === \App\Models\DemandeMateriel::STATUT_BROUILLON)
                    <a
                        href="{{ route('it.demandes.edit', $demande) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        <x-ui.icon name="pencil" class="w-4 h-4 text-content-secondary" />
                        Modifier
                    </a>
                @endif

                <!-- Dupliquer -->
                <form method="POST" action="{{ route('it.demandes.duplicate', $demande) }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                        title="Dupliquer la demande pour préparer un nouvel achat similaire"
                    >
                        <x-ui.icon name="copy" class="w-4 h-4 text-content-secondary" />
                        Dupliquer
                    </button>
                </form>

                <!-- Archiver -->
                @if (!$demande->est_archive)
                    <form method="POST" action="{{ route('it.demandes.archive', $demande) }}" class="inline" onsubmit="return confirm('Archiver cette demande ?');">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content-secondary hover:text-rose-600 hover:bg-rose-50 transition-colors"
                            title="Archiver la demande"
                        >
                            <x-ui.icon name="archive" class="w-4 h-4" />
                            Archiver
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- ACTION PRINCIPALE : BOUTON OUVRIR DANS GMAIL -->
        <div class="mb-8 p-6 bg-gradient-to-r from-primary to-primary-light rounded-2xl text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="mail" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Transmission de la demande via Gmail</h3>
                    <p class="text-xs text-blue-100 mt-1 max-w-xl leading-relaxed">
                        Le clic ouvre l'interface officielle de Gmail avec le destinataire, l'objet et le corps de message préremplis.
                        <strong>Vous pourrez relire le message et cliquer vous-même sur Envoyer dans Gmail.</strong>
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0">
                <button
                    type="button"
                    @click="openGmail()"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-accent hover:bg-accent-light text-white text-xs font-bold shadow-md transition-all inline-flex items-center justify-center gap-2.5 hover:scale-[1.02] active:scale-[0.98]"
                >
                    <x-ui.icon name="external-link" class="w-4 h-4" />
                    Ouvrir dans Gmail
                </button>
            </div>
        </div>

        <!-- Notification de statut d'ouverture -->
        <div x-show="isOpened" class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center gap-3">
            <x-ui.icon name="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0" />
            <div>
                <span class="font-bold">Ouverture Gmail effectuée</span> le <span class="font-semibold" x-text="dateOuverture"></span>.
                <span class="text-emerald-700/80">Rappel : l'envoi effectif du message dépend de votre validation finale dans Gmail.</span>
            </div>
        </div>

        <!-- Card Récapitulative du matériel -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden mb-8">
            <div class="p-6 border-b border-border bg-surface/40">
                <h3 class="text-xs font-bold text-content uppercase tracking-wider">Synthèse du matériel demandé</h3>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Produit -->
                    <div class="p-4 rounded-xl border border-border bg-white">
                        <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Désignation de l'article</span>
                        <div class="text-base font-bold text-content">
                            {{ $demande->produit_designation ?? ($demande->produit->designation ?? 'N/A') }}
                        </div>
                        <div class="text-xs text-content-secondary mt-1">
                            Référence SKU : <span class="font-mono font-semibold text-content">{{ $demande->produit_reference ?? $demande->produit->reference }}</span>
                        </div>
                        <div class="text-xs text-content-secondary mt-0.5">
                            Catégorie : {{ $demande->produit_categorie ?? 'Non classé' }}
                        </div>
                    </div>

                    <!-- Quantité & Priorité -->
                    <div class="p-4 rounded-xl border border-border bg-white flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Quantité requise</span>
                            <div class="text-3xl font-extrabold text-content">
                                {{ $demande->quantite }} <span class="text-sm font-normal text-content-secondary">unité(s)</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-border flex items-center justify-between text-xs">
                            <span class="text-content-secondary">Niveau de priorité :</span>
                            @if ($demande->priorite === 'urgente')
                                <span class="inline-flex items-center gap-1 font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                    <x-ui.icon name="alert-triangle" class="w-3.5 h-3.5" />
                                    Urgente
                                </span>
                            @elseif ($demande->priorite === 'importante')
                                <span class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    <x-ui.icon name="alert-circle" class="w-3.5 h-3.5" />
                                    Importante
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                                    Normale
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Justification -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border">
                    <div>
                        <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Motif de la demande</span>
                        <div class="text-xs text-content bg-surface p-3.5 rounded-xl border border-border leading-relaxed font-medium">
                            {{ $demande->motif }}
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Destinataire configuré</span>
                        <div class="text-xs text-content bg-surface p-3.5 rounded-xl border border-border">
                            <div class="font-semibold">{{ $demande->destinataire_nom ?? 'Direction Générale' }}</div>
                            <div class="font-mono text-content-secondary text-[11px] mt-0.5">{{ $demande->email_destinataire }}</div>
                        </div>
                    </div>

                    @if($demande->commentaire)
                        <div class="md:col-span-2">
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Observations complémentaires</span>
                            <div class="text-xs text-content bg-surface p-3.5 rounded-xl border border-border leading-relaxed">
                                {{ $demande->commentaire }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contenu exact de l'e-mail -->
        <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden mb-8">
            <div class="p-6 border-b border-border bg-surface/40 flex items-center justify-between">
                <h3 class="text-xs font-bold text-content uppercase tracking-wider flex items-center gap-2">
                    <x-ui.icon name="file-text" class="w-4 h-4 text-primary" />
                    Contenu du message injecté dans Gmail
                </h3>

                <button
                    type="button"
                    @click="navigator.clipboard.writeText(`{{ addslashes($demande->corps_email) }}`); alert('Corps de l\'e-mail copié dans le presse-papier !');"
                    class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1"
                >
                    <x-ui.icon name="copy" class="w-3.5 h-3.5" />
                    Copier le texte
                </button>
            </div>

            <div class="p-6 sm:p-8 space-y-4">
                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Objet</span>
                    <div class="p-3 bg-surface rounded-xl border border-border text-xs font-semibold text-content">
                        {{ $demande->objet_email }}
                    </div>
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Corps</span>
                    <div class="p-4 bg-surface rounded-xl border border-border text-xs font-mono text-content whitespace-pre-wrap leading-relaxed">
                        {{ $demande->corps_email }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Lien de secours direct -->
        <div class="text-center text-xs text-content-secondary pb-8">
            Un problème avec le bouton ? Vous pouvez aussi utiliser ce
            <a :href="gmailUrl" target="_blank" rel="noopener noreferrer" class="text-primary font-semibold hover:underline">
                lien de secours direct vers Gmail
            </a>.
        </div>
    </div>

</x-layouts.dashboard>
