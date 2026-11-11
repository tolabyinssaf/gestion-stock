<x-layouts.dashboard title="Supervision Administrateur" :breadcrumbs="['Administration' => null, 'Tableau de bord' => null]">

    <!-- Welcome Banner Card -->
    <div class="mb-8 p-6 sm:p-8 bg-white rounded-2xl border border-border shadow-xs relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-primary/5 rounded-full pointer-events-none blur-2xl"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 shadow-xs">
                    <x-ui.icon name="shield-check" class="w-7 h-7" />
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-bold text-content tracking-tight">
                            Bonjour, {{ Auth::user()->name }}
                        </h2>
                        <x-ui.badge variant="admin" size="sm" :dot="true">
                            Administrateur
                        </x-ui.badge>
                    </div>
                    <p class="text-xs sm:text-sm text-content-secondary mt-1 max-w-2xl leading-relaxed">
                        Bienvenue dans votre console de supervision générale. Supervisez les utilisateurs, les accès et le statut global du parc informatique.
                    </p>
                </div>
            </div>

            <!-- Meta Info Badge -->
            <div class="flex items-center gap-4 text-xs text-content-secondary bg-surface p-3.5 rounded-xl border border-border/80 self-start lg:self-center">
                <div class="flex items-center gap-2">
                    <x-ui.icon name="activity" class="w-4 h-4 text-primary" />
                    <div>
                        <span class="block text-[10px] text-content-secondary/70 uppercase font-semibold">Dernière activité</span>
                        <span class="font-medium text-content">{{ $lastActivity }}</span>
                    </div>
                </div>
                <div class="h-6 w-px bg-border"></div>
                <div>
                    <span class="block text-[10px] text-content-secondary/70 uppercase font-semibold">Statut système</span>
                    <span class="inline-flex items-center gap-1 font-medium text-emerald-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Opérationnel
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Supervision des utilisateurs -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                    Supervision des utilisateurs & Accès
                </h3>
                <p class="text-xs text-content-secondary mt-0.5">Comptes configurés et répartition des privilèges système</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-primary hover:underline">
                Gérer les utilisateurs &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Total Users -->
            <div class="bg-white rounded-xl border border-border p-4 shadow-2xs">
                <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Total comptes</span>
                <span class="text-2xl font-bold text-content mt-1 block">{{ $totalUsers }}</span>
            </div>

            <!-- 2. Actifs -->
            <div class="bg-white rounded-xl border border-border p-4 shadow-2xs">
                <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Utilisateurs actifs</span>
                <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $activeUsers }}</span>
            </div>

            <!-- 3. Désactivés -->
            <div class="bg-white rounded-xl border border-border p-4 shadow-2xs">
                <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Comptes suspendus</span>
                <span class="text-2xl font-bold text-slate-500 mt-1 block">{{ $inactiveUsers }}</span>
            </div>

            <!-- 4. Administrateurs -->
            <div class="bg-white rounded-xl border border-border p-4 shadow-2xs">
                <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Administrateurs</span>
                <span class="text-2xl font-bold text-primary mt-1 block">{{ $adminCount }}</span>
            </div>

            <!-- 5. Responsables IT -->
            <div class="bg-white rounded-xl border border-border p-4 shadow-2xs">
                <span class="text-xs font-medium text-content-secondary uppercase tracking-wider block">Responsables IT</span>
                <span class="text-2xl font-bold text-accent mt-1 block">{{ $itCount }}</span>
            </div>
        </div>
    </div>

    <!-- Section 2: Supervision du Parc & Stocks (Consultatif) -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                    Supervision globale du parc informatique
                </h3>
                <p class="text-xs text-content-secondary mt-0.5">Indicateurs généraux du matériel et alertes de continuité</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <x-ui.stat-card
                title="Total des équipements"
                :value="$totalEquipements"
                icon="hard-drive"
                color="primary"
                subtext="Machines et matériels sérialisés"
            />

            <x-ui.stat-card
                title="Références catalogue"
                :value="$totalProduits"
                icon="package"
                color="secondary"
                subtext="Modèles informatiques actifs"
            />

            <x-ui.stat-card
                title="Produits en rupture"
                :value="$ruptureCount"
                icon="alert-circle"
                color="{{ $ruptureCount > 0 ? 'rose' : 'emerald' }}"
                subtext="{{ $ruptureCount > 0 ? 'Articles sans stock disponible' : 'Aucune rupture critique' }}"
            />
        </div>
    </div>

    <!-- Section 3: Accès rapides Administrateur -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-content uppercase tracking-wider">
                    Modules d'administration
                </h3>
                <p class="text-xs text-content-secondary mt-0.5">Accès direct aux fonctionnalités de gestion et de paramétrage</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.quick-action
                title="Gestion des utilisateurs"
                description="Créer des comptes, réinitialiser des mots de passe et gérer les rôles."
                icon="users"
                color="primary"
                :available="true"
                :href="route('admin.users.index')"
            />

            <x-ui.quick-action
                title="Journal d'activités"
                description="Consulter l'historique d'audit et les opérations enregistrées."
                icon="activity"
                color="primary"
                :available="true"
                :href="route('admin.activities.index')"
            />

            <x-ui.quick-action
                title="Paramètres de l'entreprise"
                description="Configurer l'e-mail de la Direction Générale et les coordonnées."
                icon="settings"
                color="accent"
                :available="true"
                :href="route('admin.settings.index')"
            />

            <x-ui.quick-action
                title="Mon profil utilisateur"
                description="Mettre à jour mes informations personnelles et mot de passe."
                icon="user"
                color="secondary"
                :available="true"
                :href="route('profile.edit')"
            />
        </div>
    </div>

    <!-- Section 4: Journal d'activité récente -->
    <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden mb-8">
        <div class="p-5 border-b border-border bg-surface/50 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-content uppercase tracking-wider flex items-center gap-2">
                    <x-ui.icon name="activity" class="w-4 h-4 text-primary" />
                    Journal des activités récentes enregistrées
                </h3>
                <p class="text-[11px] text-content-secondary mt-0.5">Historique réel des événements générés dans l'application</p>
            </div>
        </div>

        <div class="divide-y divide-border">
            @forelse ($recentActivities as $act)
                <div class="p-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-surface/40 transition-colors">
                    <div class="flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl {{ $act['type'] === 'user' ? 'bg-primary/10 text-primary' : 'bg-accent/10 text-accent' }} flex items-center justify-center flex-shrink-0">
                            <x-ui.icon :name="$act['type'] === 'user' ? 'user-plus' : 'arrow-right-left'" class="w-4 h-4" />
                        </div>
                        <div>
                            <div class="text-xs font-bold text-content">{{ $act['titre'] }}</div>
                            <div class="text-[11px] text-content-secondary mt-0.5">{{ $act['description'] }}</div>
                        </div>
                    </div>
                    <div class="text-right text-xs text-content-secondary whitespace-nowrap">
                        {{ $act['date']->format('d/m/Y H:i') }}
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-content-secondary italic">
                    Aucune activité récente enregistrée.
                </div>
            @endforelse
        </div>
    </div>

</x-layouts.dashboard>
