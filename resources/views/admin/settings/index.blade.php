<x-layouts.dashboard title="Paramètres de l'application" :breadcrumbs="['Administration' => route('admin.dashboard'), 'Paramètres' => null]">

    <div class="max-w-5xl mx-auto" x-data="{ activeTab: 'general' }">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-emerald-800 text-sm">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
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

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="settings" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content tracking-tight">Paramètres de l'application</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-xl">
                        Configuration de l'identité d'entreprise, coordonnées de la Direction pour Gmail et préférences globales.
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 mb-6 border-b border-border">
            <button
                type="button"
                @click="activeTab = 'general'"
                class="px-5 py-3 text-xs font-bold transition-colors border-b-2 flex items-center gap-2"
                :class="activeTab === 'general' ? 'border-primary text-primary' : 'border-transparent text-content-secondary hover:text-content'"
            >
                <x-ui.icon name="building" class="w-4 h-4" />
                Informations générales
            </button>
            <button
                type="button"
                @click="activeTab = 'gmail'"
                class="px-5 py-3 text-xs font-bold transition-colors border-b-2 flex items-center gap-2"
                :class="activeTab === 'gmail' ? 'border-primary text-primary' : 'border-transparent text-content-secondary hover:text-content'"
            >
                <x-ui.icon name="mail" class="w-4 h-4" />
                Configuration Gmail (Direction)
            </button>
            <button
                type="button"
                @click="activeTab = 'preferences'"
                class="px-5 py-3 text-xs font-bold transition-colors border-b-2 flex items-center gap-2"
                :class="activeTab === 'preferences' ? 'border-primary text-primary' : 'border-transparent text-content-secondary hover:text-content'"
            >
                <x-ui.icon name="sliders" class="w-4 h-4" />
                Préférences d'affichage
            </button>
        </div>

        <!-- Settings Form -->
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <!-- TAB 1: INFORMATIONS GÉNÉRALES -->
            <div x-show="activeTab === 'general'" class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8 space-y-6">
                <div class="border-b border-border pb-4 mb-4">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1">Identité de l'entreprise</h3>
                    <p class="text-xs text-content-secondary">Informations utilisées dans les rapports, bordereaux et mentions d'application.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nom_application" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Nom de l'application <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nom_application"
                            name="nom_application"
                            value="{{ old('nom_application', $settings['nom_application']) }}"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <div>
                        <label for="nom_entreprise" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Nom de l'entreprise
                        </label>
                        <input
                            type="text"
                            id="nom_entreprise"
                            name="nom_entreprise"
                            value="{{ old('nom_entreprise', $settings['nom_entreprise']) }}"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <div>
                        <label for="email_contact" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            E-mail de contact général
                        </label>
                        <input
                            type="email"
                            id="email_contact"
                            name="email_contact"
                            value="{{ old('email_contact', $settings['email_contact']) }}"
                            placeholder="contact@entreprise.com"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <div>
                        <label for="telephone_entreprise" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Téléphone standard
                        </label>
                        <input
                            type="text"
                            id="telephone_entreprise"
                            name="telephone_entreprise"
                            value="{{ old('telephone_entreprise', $settings['telephone_entreprise']) }}"
                            placeholder="+33 1 23 45 67 89"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="adresse_entreprise" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Adresse postale du siège
                        </label>
                        <input
                            type="text"
                            id="adresse_entreprise"
                            name="adresse_entreprise"
                            value="{{ old('adresse_entreprise', $settings['adresse_entreprise']) }}"
                            placeholder="12 rue de l'Informatique, 75001 Paris"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>
                </div>
            </div>

            <!-- TAB 2: CONFIGURATION GMAIL -->
            <div x-show="activeTab === 'gmail'" style="display: none;" class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8 space-y-6">
                <div class="border-b border-border pb-4 mb-4">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1">Destinataire des demandes de matériel</h3>
                    <p class="text-xs text-content-secondary">Coordonnées injectées automatiquement dans les URL de composition de message Gmail.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="directeur_nom" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Nom du directeur / Destinataire <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="directeur_nom"
                            name="directeur_nom"
                            value="{{ old('directeur_nom', $settings['directeur_nom']) }}"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <div>
                        <label for="directeur_email" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Adresse e-mail du directeur <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="directeur_email"
                            name="directeur_email"
                            value="{{ old('directeur_email', $settings['directeur_email']) }}"
                            required
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="signature_service_it" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Signature par défaut du service IT
                        </label>
                        <input
                            type="text"
                            id="signature_service_it"
                            name="signature_service_it"
                            value="{{ old('signature_service_it', $settings['signature_service_it']) }}"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>
                </div>
            </div>

            <!-- TAB 3: PRÉFÉRENCES D'AFFICHAGE -->
            <div x-show="activeTab === 'preferences'" style="display: none;" class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8 space-y-6">
                <div class="border-b border-border pb-4 mb-4">
                    <h3 class="text-sm font-bold text-content uppercase tracking-wider mb-1">Préférences système</h3>
                    <p class="text-xs text-content-secondary">Paramètres par défaut pour la pagination et le format des dates.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="lignes_par_page" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Lignes par page (Pagination) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="lignes_par_page"
                            name="lignes_par_page"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                        >
                            <option value="10" {{ old('lignes_par_page', $settings['lignes_par_page']) == 10 ? 'selected' : '' }}>10 lignes</option>
                            <option value="15" {{ old('lignes_par_page', $settings['lignes_par_page']) == 15 ? 'selected' : '' }}>15 lignes (Par défaut)</option>
                            <option value="20" {{ old('lignes_par_page', $settings['lignes_par_page']) == 20 ? 'selected' : '' }}>20 lignes</option>
                            <option value="50" {{ old('lignes_par_page', $settings['lignes_par_page']) == 50 ? 'selected' : '' }}>50 lignes</option>
                            <option value="100" {{ old('lignes_par_page', $settings['lignes_par_page']) == 100 ? 'selected' : '' }}>100 lignes</option>
                        </select>
                    </div>

                    <div>
                        <label for="format_date" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Format d'affichage des dates <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="format_date"
                            name="format_date"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                        >
                            <option value="d/m/Y" {{ old('format_date', $settings['format_date']) === 'd/m/Y' ? 'selected' : '' }}>JJ/MM/AAAA (ex: 25/12/2026)</option>
                            <option value="Y-m-d" {{ old('format_date', $settings['format_date']) === 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-JJ (ISO 8601)</option>
                            <option value="d-m-Y" {{ old('format_date', $settings['format_date']) === 'd-m-Y' ? 'selected' : '' }}>JJ-MM-AAAA</option>
                        </select>
                    </div>

                    <div>
                        <label for="fuseau_horaire" class="block text-xs font-bold text-content uppercase tracking-wider mb-2">
                            Fuseau horaire d'affichage <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="fuseau_horaire"
                            name="fuseau_horaire"
                            class="w-full px-3.5 py-2.5 bg-surface/50 border border-border rounded-xl text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20"
                        >
                            <option value="Europe/Paris" {{ old('fuseau_horaire', $settings['fuseau_horaire']) === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (UTC+1/+2)</option>
                            <option value="UTC" {{ old('fuseau_horaire', $settings['fuseau_horaire']) === 'UTC' ? 'selected' : '' }}>UTC (Temps universel)</option>
                            <option value="Africa/Casablanca" {{ old('fuseau_horaire', $settings['fuseau_horaire']) === 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca</option>
                            <option value="America/New_York" {{ old('fuseau_horaire', $settings['fuseau_horaire']) === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Save Action Button -->
            <div class="mt-6 bg-white rounded-2xl border border-border p-5 flex items-center justify-between">
                <span class="text-xs text-content-secondary">
                    Toutes les modifications sont immédiatement appliquées et journalisées dans l'audit.
                </span>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs font-semibold shadow-xs transition-colors"
                >
                    <x-ui.icon name="check" class="w-4 h-4" />
                    Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>

</x-layouts.dashboard>
