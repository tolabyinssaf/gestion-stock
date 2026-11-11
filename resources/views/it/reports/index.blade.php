<x-layouts.dashboard title="Rapports & Exports" :breadcrumbs="['Rapports & Exports' => null]">

    <div x-data="reportsHub()">
        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="bar-chart-3" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Rapports & Exports</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-2xl">
                        Consultez, exportez et transmettez par Gmail les rapports de gestion du stock et des mouvements de matériel.
                    </p>
                </div>
            </div>

            <!-- Email recipient info indicator -->
            <div class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-surface border border-border text-xs text-content-secondary self-start md:self-center">
                <x-ui.icon name="mail" class="w-4 h-4 text-primary" />
                <span>Destinataire des rapports : <strong class="text-content">{{ $destinataireEmail }}</strong> ({{ $destinataireNom }})</span>
            </div>
        </div>

        <!-- 2 Core Stock Reports Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Rapport 1: Rapport de stock -->
            <div class="bg-white rounded-2xl border border-border shadow-xs p-6 flex flex-col justify-between hover:border-primary/40 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <x-ui.icon name="boxes" class="w-6 h-6" />
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-primary/10 text-primary">
                            Stock & Inventaire
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-content mb-1.5">1. Rapport de stock</h3>
                    <p class="text-xs text-content-secondary leading-relaxed mb-6">
                        Inventaire complet des produits, catégories, quantités disponibles, emplacements de stockage et indicateurs d'état (disponible, stock faible, rupture).
                    </p>

                    <!-- Features list -->
                    <div class="space-y-2 mb-6 text-xs text-content-secondary bg-surface/50 p-3.5 rounded-xl border border-border/60">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" />
                            <span>Suivi en temps réel des unités en stock</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" />
                            <span>Filtres par catégorie, emplacement et niveau d'alerte</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" />
                            <span>Export certifié et transmission par email</span>
                        </div>
                    </div>
                </div>

                <!-- 3 Actions: Consulter, Exporter (Excel/PDF), Envoyer -->
                <div class="pt-5 border-t border-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <!-- Action 1: Consulter -->
                    <a
                        href="{{ route('it.reports.inventaire') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-bold shadow-xs transition-colors"
                    >
                        <x-ui.icon name="eye" class="w-4 h-4" />
                        Consulter
                    </a>

                    <div class="flex items-center gap-2 justify-end">
                        <!-- Action 2: Exporter Menu / Buttons -->
                        <div class="relative" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                @click.outside="open = false"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                            >
                                <x-ui.icon name="download" class="w-4 h-4 text-content-secondary" />
                                <span>Exporter</span>
                                <x-ui.icon name="chevron-down" class="w-3.5 h-3.5 text-content-secondary" />
                            </button>
                            <div
                                x-show="open"
                                x-transition
                                class="absolute right-0 bottom-full mb-2 w-48 bg-white rounded-xl shadow-lg border border-border p-1.5 z-20 space-y-1"
                                style="display: none;"
                            >
                                <a
                                    href="{{ route('it.reports.inventaire', ['export' => 'pdf']) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    <x-ui.icon name="file-text" class="w-4 h-4 text-rose-600" />
                                    <span>Télécharger PDF</span>
                                </a>
                                <a
                                    href="{{ route('it.reports.inventaire', ['export' => 'excel']) }}"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    <x-ui.icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" />
                                    <span>Exporter Excel (.csv)</span>
                                </a>
                            </div>
                        </div>

                        <!-- Action 3: Envoyer par Gmail -->
                        <button
                            type="button"
                            @click="handleSend('{{ route('it.reports.inventaire', ['action' => 'get_gmail_url']) }}', '{{ route('it.reports.inventaire', ['export' => 'pdf']) }}', 'Rapport de stock')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-primary/30 bg-primary/5 hover:bg-primary/10 text-primary text-xs font-bold transition-colors"
                            title="Télécharger le PDF et préparer le courriel Gmail"
                        >
                            <x-ui.icon name="send" class="w-4 h-4" />
                            <span>Envoyer</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rapport 2: Rapport des mouvements -->
            <div class="bg-white rounded-2xl border border-border shadow-xs p-6 flex flex-col justify-between hover:border-accent/40 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center">
                            <x-ui.icon name="history" class="w-6 h-6" />
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-accent/10 text-accent">
                            Traçabilité & Flux
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-content mb-1.5">2. Rapport des mouvements</h3>
                    <p class="text-xs text-content-secondary leading-relaxed mb-6">
                        Journal complet des entrées, sorties, transferts inter-magasins et corrections de stock, avec dates précises, quantités et utilisateurs responsables.
                    </p>

                    <!-- Features list -->
                    <div class="space-y-2 mb-6 text-xs text-content-secondary bg-surface/50 p-3.5 rounded-xl border border-border/60">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" />
                            <span>Traçabilité rigoureuse de chaque unité déplacée</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" />
                            <span>Filtres par date (période), type d'opération et motif</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" />
                            <span>Audit des utilisateurs à l'origine de l'opération</span>
                        </div>
                    </div>
                </div>

                <!-- 3 Actions: Consulter, Exporter (Excel/PDF), Envoyer -->
                <div class="pt-5 border-t border-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <!-- Action 1: Consulter -->
                    <a
                        href="{{ route('it.reports.mouvements') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-accent hover:bg-accent/90 text-white text-xs font-bold shadow-xs transition-colors"
                    >
                        <x-ui.icon name="eye" class="w-4 h-4" />
                        Consulter
                    </a>

                    <div class="flex items-center gap-2 justify-end">
                        <!-- Action 2: Exporter Menu / Buttons -->
                        <div class="relative" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                @click.outside="open = false"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
                            >
                                <x-ui.icon name="download" class="w-4 h-4 text-content-secondary" />
                                <span>Exporter</span>
                                <x-ui.icon name="chevron-down" class="w-3.5 h-3.5 text-content-secondary" />
                            </button>
                            <div
                                x-show="open"
                                x-transition
                                class="absolute right-0 bottom-full mb-2 w-48 bg-white rounded-xl shadow-lg border border-border p-1.5 z-20 space-y-1"
                                style="display: none;"
                            >
                                <a
                                    href="{{ route('it.reports.mouvements', ['export' => 'pdf']) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    <x-ui.icon name="file-text" class="w-4 h-4 text-rose-600" />
                                    <span>Télécharger PDF</span>
                                </a>
                                <a
                                    href="{{ route('it.reports.mouvements', ['export' => 'excel']) }}"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-content hover:bg-surface transition-colors"
                                >
                                    <x-ui.icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" />
                                    <span>Exporter Excel (.csv)</span>
                                </a>
                            </div>
                        </div>

                        <!-- Action 3: Envoyer par Gmail -->
                        <button
                            type="button"
                            @click="handleSend('{{ route('it.reports.mouvements', ['action' => 'get_gmail_url']) }}', '{{ route('it.reports.mouvements', ['export' => 'pdf']) }}', 'Rapport des mouvements')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-accent/30 bg-accent/5 hover:bg-accent/10 text-accent text-xs font-bold transition-colors"
                            title="Télécharger le PDF et préparer le courriel Gmail"
                        >
                            <x-ui.icon name="send" class="w-4 h-4" />
                            <span>Envoyer</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal de confirmation / information pour l'action Envoyer -->
        <div
            x-show="showSendModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-2xs flex items-center justify-center p-4"
            x-transition
            style="display: none;"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-border">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="mail" class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-content">Transmission du rapport via Gmail</h4>
                        <p class="text-xs text-content-secondary mt-0.5" x-text="currentReportTitle"></p>
                    </div>
                </div>

                <div class="space-y-3 mb-6 text-xs text-content">
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 space-y-1.5">
                        <p class="font-bold flex items-center gap-1.5">
                            <x-ui.icon name="info" class="w-4 h-4 text-primary" />
                            Comment fonctionne l'envoi ?
                        </p>
                        <ol class="list-decimal list-inside space-y-1 text-[11px] text-blue-800">
                            <li>Le <strong>document PDF</strong> est ouvert dans un nouvel onglet pour impression / téléchargement.</li>
                            <li>Un <strong>courriel prérempli</strong> s'ouvre dans Gmail avec le destinataire configuré (<span class="font-mono" x-text="destinataireEmail"></span>).</li>
                            <li><strong>Important :</strong> Vous devez <strong>joindre manuellement le PDF</strong> téléchargé dans Gmail avant de cliquer sur Envoyer.</li>
                        </ol>
                    </div>
                    <p class="text-content-secondary text-[11px] italic">
                        L'application ne prétend pas que l'email est expédié automatiquement : vous gardez le contrôle final de l'envoi dans Gmail.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="showSendModal = false"
                        class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-content hover:bg-surface transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        @click="executeSend()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-bold transition-colors shadow-xs"
                    >
                        <x-ui.icon name="external-link" class="w-4 h-4" />
                        Générer le PDF & Ouvrir Gmail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function reportsHub() {
            return {
                showSendModal: false,
                currentReportTitle: '',
                currentApiUrl: '',
                currentPdfUrl: '',
                destinataireEmail: '{{ $destinataireEmail }}',
                handleSend(apiUrl, pdfUrl, title) {
                    this.currentApiUrl = apiUrl;
                    this.currentPdfUrl = pdfUrl;
                    this.currentReportTitle = title;
                    this.showSendModal = true;
                },
                executeSend() {
                    const pdfUrl = this.currentPdfUrl;
                    const apiUrl = this.currentApiUrl;
                    this.showSendModal = false;

                    // 1. Ouvrir le PDF dans un nouvel onglet (pour que l'utilisateur puisse l'enregistrer / l'imprimer)
                    window.open(pdfUrl, '_blank');

                    // 2. Récupérer l'URL Gmail préremplie et l'ouvrir
                    fetch(apiUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.gmail_url) {
                            setTimeout(() => {
                                window.open(data.gmail_url, '_blank');
                            }, 500);
                        }
                    })
                    .catch(err => {
                        console.error('Erreur lors de la préparation Gmail :', err);
                    });
                }
            };
        }
    </script>

</x-layouts.dashboard>
