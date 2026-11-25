@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'file-text'      => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M10 9H8|M16 13H8|M16 17H8',
        'arrow-left'     => 'm12 19-7-7 7-7|M19 12H5',
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'alert-circle'   => 'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0z|M12 8v4|M12 16h.01',
        'check-circle'   => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'pencil'         => 'M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z|m15 5 4 4',
        'copy'           => 'M20 9h-9a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z|M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1',
        'archive'        => 'M4 3h16a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8|M10 12h4',
        'mail'           => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'external-link'  => 'M15 3h6v6|M10 14 21 3|M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6',
        'box'            => 'M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z|m3.3 7 8.7 5 8.7-5|M12 22V12',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
@endphp

<x-layouts.dashboard title="Détail de la demande" :breadcrumbs="['Demandes de matériel' => route('it.demandes.index'), 'Fiche '.$demande->reference => null]">

    <div
        class="max-w-5xl mx-auto"
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
        <!-- Messages flash -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                {!! $svg('check-circle', 'w-5 h-5 text-emerald-600 flex-shrink-0') !!}
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Alerte : popup bloquée -->
        <div id="popupBlockedNotice" style="display: none;" class="mb-5 p-4 bg-amber-50 rounded-2xl text-amber-900 text-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    {!! $svg('alert-circle', 'w-4 h-4 text-amber-600 flex-shrink-0') !!}
                    <span>Votre navigateur a bloqué l'ouverture automatique du nouvel onglet.</span>
                </div>
                <a :href="gmailUrl" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-full font-semibold transition-colors">
                    Ouvrir Gmail manuellement &rarr;
                </a>
            </div>
        </div>

        <!-- En-tête -->
        <div class="mb-6 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-6">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('file-text', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-content tracking-tight">Demande de matériel</h1>
                        <span class="font-mono text-xs font-bold px-3 py-1 rounded-full bg-[#0092FD]/10 text-[#0092FD]">
                            {{ $demande->reference }}
                        </span>

                        <!-- Badge Statut -->
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                            :class="{
                                'bg-slate-100 text-slate-700': statusLabel === 'Brouillon',
                                'bg-amber-50 text-amber-700': statusLabel === 'Préparée',
                                'bg-blue-50 text-blue-700': statusLabel === 'Ouverte dans Gmail',
                                'bg-emerald-50 text-emerald-700': statusLabel.includes('Envoyée')
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

            <!-- Barre d'actions -->
            <div class="mt-5 pt-4 border-t border-border/60 flex flex-col sm:flex-row sm:items-center gap-2.5 flex-wrap">
                <a
                    href="{{ route('it.demandes.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-white text-xs font-semibold text-content shadow-[0_2px_10px_rgba(2,45,119,0.08)] hover:shadow-[0_4px_16px_rgba(2,45,119,0.14)] hover:text-[#0092FD] transition-all"
                >
                    {!! $svg('arrow-left', 'w-4 h-4') !!}
                    Retour
                </a>

                @if (in_array($demande->statut_preparation, ['prete', 'ouverture_gmail']))
                    <form method="POST" action="{{ route('it.demandes.mark-sent', $demande) }}" class="flex" onsubmit="return confirm('Confirmez-vous avoir envoyé cet e-mail depuis votre messagerie Gmail ? Cette action est déclarative.');">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-emerald-50 text-xs font-semibold text-emerald-700 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer"
                            title="Indiquer que l'e-mail a bien été envoyé dans Gmail"
                        >
                            {!! $svg('check-circle', 'w-4 h-4') !!}
                            Marquer comme envoyée (déclaratif)
                        </button>
                    </form>
                @endif

                @if ($demande->statut_preparation === \App\Models\DemandeMateriel::STATUT_BROUILLON)
                    <a
                        href="{{ route('it.demandes.edit', $demande) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-[#0092FD]/10 text-[#0092FD] hover:bg-[#0092FD] hover:text-white text-xs font-semibold transition-colors"
                    >
                        {!! $svg('pencil', 'w-4 h-4') !!}
                        Modifier
                    </a>
                @endif

                <!-- Dupliquer -->
                <form method="POST" action="{{ route('it.demandes.duplicate', $demande) }}" class="flex">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-surface hover:bg-slate-100 text-xs font-semibold text-content transition-colors cursor-pointer"
                        title="Dupliquer la demande pour préparer un nouvel achat similaire"
                    >
                        {!! $svg('copy', 'w-4 h-4 text-content-secondary') !!}
                        Dupliquer
                    </button>
                </form>

                <!-- Archiver -->
                @if (!$demande->est_archive)
                    <form method="POST" action="{{ route('it.demandes.archive', $demande) }}" class="flex" onsubmit="return confirm('Archiver cette demande ?');">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-surface text-xs font-semibold text-content-secondary hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                            title="Archiver la demande"
                        >
                            {!! $svg('archive', 'w-4 h-4') !!}
                            Archiver
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- ACTION PRINCIPALE : OUVRIR DANS GMAIL -->
        <div class="mb-6 p-5 sm:p-6 bg-gradient-to-r from-[#0092FD]/10 via-white to-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('mail', 'w-5 h-5 sm:w-6 sm:h-6') !!}
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-content">Transmission de la demande via Gmail</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-xl leading-relaxed">
                        Le clic ouvre l'interface officielle de Gmail avec le destinataire, l'objet et le corps de message préremplis.
                        <strong class="text-content">Vous pourrez relire le message et cliquer vous-même sur Envoyer dans Gmail.</strong>
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0">
                <button
                    type="button"
                    @click="openGmail()"
                    class="w-full sm:w-auto px-6 py-3 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-bold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-all inline-flex items-center justify-center gap-2.5 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                >
                    {!! $svg('external-link', 'w-4 h-4') !!}
                    Ouvrir dans Gmail
                </button>
            </div>
        </div>

        <!-- Notification de statut d'ouverture -->
        <div x-show="isOpened" x-cloak class="mb-6 p-4 bg-emerald-50 rounded-2xl text-emerald-800 text-xs flex items-start gap-3">
            {!! $svg('check-circle', 'w-5 h-5 text-emerald-600 flex-shrink-0') !!}
            <div>
                <span class="font-bold">Ouverture Gmail effectuée</span> le <span class="font-semibold" x-text="dateOuverture"></span>.
                <span class="text-emerald-700/80">Rappel : l'envoi effectif du message dépend de votre validation finale dans Gmail.</span>
            </div>
        </div>

        <!-- Synthèse du matériel -->
        <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-7 mb-6">
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-border/60">
                <div class="w-10 h-10 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                    {!! $svg('box', 'w-5 h-5') !!}
                </div>
                <h3 class="text-sm font-bold text-content uppercase tracking-wider">Synthèse du matériel demandé</h3>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                    <!-- Produit -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-surface/70">
                        <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Désignation de l'article</span>
                        <div class="text-base font-bold text-content break-words">
                            {{ $demande->produit_designation ?? ($demande->produit->designation ?? 'N/A') }}
                        </div>
                        <div class="text-xs text-content-secondary mt-1.5">
                            Référence SKU : <span class="font-mono font-semibold text-content break-all">{{ $demande->produit_reference ?? $demande->produit->reference }}</span>
                        </div>
                        <div class="text-xs text-content-secondary mt-0.5">
                            Catégorie : {{ $demande->produit_categorie ?? 'Non classé' }}
                        </div>
                    </div>

                    <!-- Quantité & Priorité -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-surface/70 flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1">Quantité requise</span>
                            <div class="text-3xl font-extrabold text-content tracking-tight">
                                {{ $demande->quantite }} <span class="text-sm font-normal text-content-secondary">unité(s)</span>
                            </div>
                            @if ($demande->prix_estime)
                                <div class="mt-2 text-xs text-content-secondary">
                                    Estimation unitaire : <strong class="text-content font-bold">{{ number_format($demande->prix_estime, 2, ',', ' ') }} € HT</strong>
                                    @if ($demande->quantite > 1)
                                        <span class="block text-[11px] text-content-secondary mt-0.5">
                                            Total estimé : <strong class="text-content font-bold">{{ number_format($demande->prix_estime * $demande->quantite, 2, ',', ' ') }} € HT</strong>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-border/60 flex items-center justify-between gap-2 text-xs">
                            <span class="text-content-secondary">Niveau de priorité :</span>
                            @if ($demande->priorite === 'urgente')
                                <span class="inline-flex items-center gap-1.5 font-bold text-rose-700 bg-rose-50 px-3 py-1 rounded-full">
                                    {!! $svg('alert-triangle', 'w-3.5 h-3.5') !!}
                                    Urgente
                                </span>
                            @elseif ($demande->priorite === 'importante')
                                <span class="inline-flex items-center gap-1.5 font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                                    {!! $svg('alert-circle', 'w-3.5 h-3.5') !!}
                                    Importante
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 font-bold text-[#0092FD] bg-[#0092FD]/10 px-3 py-1 rounded-full">
                                    Normale
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Identification de l'échange : De ... À ... -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 sm:p-5 bg-surface/70 rounded-2xl text-xs">
                    <div class="space-y-1 min-w-0">
                        <span class="text-[10px] font-extrabold text-[#0092FD] uppercase tracking-wider block">
                            📤 Expéditeur (De / Qui envoie)
                        </span>
                        <div class="font-bold text-content text-sm break-words">
                            {{ $demande->user->name ?? 'Gestionnaire de stock' }}
                        </div>
                        <div class="font-mono text-content-secondary text-[11px] break-all">
                            {{ $demande->user->email ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="space-y-1 min-w-0 border-t sm:border-t-0 sm:border-l border-border/60 pt-3 sm:pt-0 sm:pl-4">
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider block">
                            📥 Destinataire (À / Qui reçoit)
                        </span>
                        <div class="font-bold text-content text-sm break-words">
                            {{ $demande->destinataire_nom ?? 'Direction Générale' }}
                        </div>
                        <div class="font-mono text-content-secondary text-[11px] break-all">
                            {{ $demande->email_destinataire }}
                        </div>
                    </div>
                </div>

                <!-- Justification -->
                <div class="grid grid-cols-1 gap-5 pt-5 border-t border-border/60">
                    <div>
                        <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1.5">Motif de la demande</span>
                        <div class="text-xs text-content bg-surface/70 p-4 rounded-2xl leading-relaxed font-medium break-words">
                            {{ $demande->motif ?: 'Non spécifié' }}
                        </div>
                    </div>

                    @if($demande->commentaire)
                        <div>
                            <span class="text-[11px] font-bold text-content-secondary uppercase tracking-wider block mb-1.5">Observations complémentaires</span>
                            <div class="text-xs text-content bg-surface/70 p-4 rounded-2xl leading-relaxed break-words">
                                {{ $demande->commentaire }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contenu exact de l'e-mail -->
        <div class="bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] p-5 sm:p-7 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-border/60">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                        {!! $svg('file-text', 'w-5 h-5') !!}
                    </div>
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider">Contenu du message injecté dans Gmail</h3>
                </div>

                <button
                    type="button"
                    @click="navigator.clipboard.writeText(`{{ addslashes($demande->corps_email) }}`); alert('Corps de l\'e-mail copié dans le presse-papier !');"
                    class="self-start sm:self-auto inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#0092FD]/10 text-[#0092FD] hover:bg-[#0092FD] hover:text-white text-[11px] font-semibold transition-colors cursor-pointer"
                >
                    {!! $svg('copy', 'w-3.5 h-3.5') !!}
                    Copier le texte
                </button>
            </div>

            <div class="space-y-4">
                <!-- En-tête De ... À ... visuel -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-surface/70 rounded-2xl text-xs">
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold text-content-secondary uppercase block">Expéditeur (De) :</span>
                        <span class="font-semibold text-content">{{ $demande->user->name ?? 'Gestionnaire de stock' }}</span>
                        <span class="text-content-secondary font-mono text-[11px] break-all">&lt;{{ $demande->user->email ?? 'N/A' }}&gt;</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold text-content-secondary uppercase block">Destinataire (À) :</span>
                        <span class="font-semibold text-content">{{ $demande->destinataire_nom ?? 'Direction Générale' }}</span>
                        <span class="text-content-secondary font-mono text-[11px] break-all">&lt;{{ $demande->email_destinataire }}&gt;</span>
                    </div>
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1.5">Objet</span>
                    <div class="p-3.5 bg-surface/70 rounded-2xl text-xs font-semibold text-content break-words">
                        {{ $demande->objet_email }}
                    </div>
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1.5">Corps</span>
                    <div class="p-4 bg-surface/70 rounded-2xl text-xs font-mono text-content whitespace-pre-wrap break-words leading-relaxed">
                        {{ $demande->corps_email }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Lien de secours direct -->
        <div class="text-center text-xs text-content-secondary pb-8">
            Un problème avec le bouton ? Vous pouvez aussi utiliser ce
            <a :href="gmailUrl" target="_blank" rel="noopener noreferrer" class="text-[#0092FD] font-semibold hover:underline">
                lien de secours direct vers Gmail
            </a>.
        </div>
    </div>

</x-layouts.dashboard>