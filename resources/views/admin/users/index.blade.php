<x-layouts.dashboard title="Gestion des utilisateurs" :breadcrumbs="['Administration' => null, 'Utilisateurs' => null]">

    <div
        x-data="{
            // Drawer state (Create / Edit)
            drawerOpen: false,
            isEditing: false,
            drawerTitle: 'Ajouter un utilisateur',
            formAction: '{{ route('admin.users.store') }}',
            formMethod: 'POST',
            formData: {
                id: null,
                name: '',
                email: '',
                role: '{{ \App\Models\User::ROLE_RESPONSABLE_IT }}',
                status: '{{ \App\Models\User::STATUS_ACTIVE }}',
                password: ''
            },
            openCreateDrawer() {
                this.isEditing = false;
                this.drawerTitle = 'Ajouter un utilisateur';
                this.formAction = '{{ route('admin.users.store') }}';
                this.formMethod = 'POST';
                this.formData = {
                    id: null,
                    name: '',
                    email: '',
                    role: '{{ \App\Models\User::ROLE_RESPONSABLE_IT }}',
                    status: '{{ \App\Models\User::STATUS_ACTIVE }}',
                    password: ''
                };
                this.drawerOpen = true;
            },
            openEditDrawer(user) {
                this.isEditing = true;
                this.drawerTitle = 'Modifier l\'utilisateur';
                this.formAction = '/admin/users/' + user.id;
                this.formMethod = 'POST';
                this.formData = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role,
                    status: user.status,
                    password: ''
                };
                this.drawerOpen = true;
            },
            generatePassword() {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%&*';
                let pass = '';
                for (let i = 0; i < 10; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.formData.password = pass;
            },

            // Status Toggle Modal
            statusModalOpen: false,
            statusUser: { id: null, name: '', status: '' },
            openStatusModal(user) {
                this.statusUser = user;
                this.statusModalOpen = true;
            },

            // Reset Password Modal
            resetModalOpen: false,
            resetUser: { id: null, name: '' },
            resetPasswordValue: '',
            openResetModal(user) {
                this.resetUser = user;
                this.resetPasswordValue = '';
                this.resetModalOpen = true;
            },
            generateResetPassword() {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%&*';
                let pass = '';
                for (let i = 0; i < 10; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.resetPasswordValue = pass;
            }
        }"
        @keydown.escape.window="drawerOpen = false; statusModalOpen = false; resetModalOpen = false"
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

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <div class="flex items-center gap-2 mb-2 font-semibold">
                    <x-ui.icon name="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0" />
                    <span>Des erreurs sont survenues lors de la validation :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Banner -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="users" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-content">Gestion des utilisateurs</h2>
                    <p class="text-xs sm:text-sm text-content-secondary mt-0.5">
                        Administrez les comptes autorisés, attribuez les rôles et contrôlez les accès au système.
                    </p>
                </div>
            </div>

            <div>
                <x-ui.button
                    variant="primary"
                    size="md"
                    icon="plus"
                    @click="openCreateDrawer()"
                >
                    Ajouter un utilisateur
                </x-ui.button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <x-ui.stat-card
                title="Total Utilisateurs"
                :value="$totalUsers"
                icon="users"
                color="primary"
                subtext="Comptes enregistrés"
            />
            <x-ui.stat-card
                title="Comptes Actifs"
                :value="$activeUsers"
                icon="check-circle"
                color="emerald"
                subtext="Autorisés à se connecter"
            />
            <x-ui.stat-card
                title="Comptes Inactifs"
                :value="$inactiveUsers"
                icon="alert-circle"
                color="slate"
                subtext="Accès révoqué"
            />
            <x-ui.stat-card
                title="Responsables IT"
                :value="$itUsersCount"
                icon="laptop"
                color="accent"
                subtext="Gestionnaires de parc"
            />
        </div>

        <!-- Filter Toolbar -->
        <div class="mb-6 p-4 bg-white rounded-xl border border-border shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                <!-- Search input -->
                <div class="lg:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-content-secondary">
                        <x-ui.icon name="search" class="w-4 h-4" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Rechercher par nom ou adresse e-mail..."
                        class="w-full pl-9 pr-4 py-2 bg-surface/60 border border-border rounded-lg text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                    />
                </div>

                <!-- Role filter -->
                <div class="lg:col-span-3">
                    <select
                        name="role"
                        class="w-full px-3 py-2 bg-surface/60 border border-border rounded-lg text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                    >
                        <option value="">Tous les rôles</option>
                        <option value="{{ \App\Models\User::ROLE_ADMIN }}" {{ $role === \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>Administrateur</option>
                        <option value="{{ \App\Models\User::ROLE_RESPONSABLE_IT }}" {{ $role === \App\Models\User::ROLE_RESPONSABLE_IT ? 'selected' : '' }}>Responsable IT</option>
                    </select>
                </div>

                <!-- Status filter -->
                <div class="lg:col-span-2">
                    <select
                        name="status"
                        class="w-full px-3 py-2 bg-surface/60 border border-border rounded-lg text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                    >
                        <option value="">Tous les statuts</option>
                        <option value="{{ \App\Models\User::STATUS_ACTIVE }}" {{ $status === \App\Models\User::STATUS_ACTIVE ? 'selected' : '' }}>Actif</option>
                        <option value="{{ \App\Models\User::STATUS_INACTIVE }}" {{ $status === \App\Models\User::STATUS_INACTIVE ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" size="sm" class="flex-1">
                        Filtrer
                    </x-ui.button>
                    @if ($search || $role || $status)
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="p-2 text-content-secondary hover:text-content hover:bg-surface rounded-lg transition-colors"
                            title="Réinitialiser les filtres"
                        >
                            <x-ui.icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl border border-border shadow-xs overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-surface/80 border-b border-border text-[11px] uppercase tracking-wider text-content-secondary font-semibold">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">Utilisateur</th>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">E-mail</th>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">Rôle</th>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">Statut</th>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">Dernière connexion</th>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap">Créé le</th>
                            <th scope="col" class="px-5 py-3.5 whitespace-nowrap text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-content">
                        @forelse ($users as $user)
                            <tr class="hover:bg-surface/50 transition-colors">
                                <!-- User Name + Avatar -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs text-white shadow-xs select-none {{ $user->isAdmin() ? 'bg-primary' : 'bg-accent' }}">
                                            {{ $user->initials }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-content block text-xs">{{ $user->name }}</span>
                                            @if (Auth::id() === $user->id)
                                                <span class="text-[10px] text-primary font-medium">(Vous)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-content-secondary">
                                    {{ $user->email }}
                                </td>

                                <!-- Role -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-ui.badge :variant="$user->isAdmin() ? 'admin' : 'it'" size="sm">
                                        {{ $user->role_label }}
                                    </x-ui.badge>
                                </td>

                                <!-- Status -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-ui.badge :variant="$user->isActive() ? 'disponible' : 'neutral'" size="sm" :dot="true">
                                        {{ $user->status_label }}
                                    </x-ui.badge>
                                </td>

                                <!-- Last Login -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-content-secondary">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}
                                </td>

                                <!-- Created at -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-content-secondary">
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : '&mdash;' }}
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Edit button -->
                                        <button
                                            type="button"
                                            @click="openEditDrawer({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                role: '{{ $user->role }}',
                                                status: '{{ $user->status }}'
                                            })"
                                            class="p-1.5 rounded-lg text-content-secondary hover:text-primary hover:bg-surface transition-colors"
                                            title="Modifier"
                                        >
                                            <x-ui.icon name="wrench" class="w-4 h-4" />
                                        </button>

                                        <!-- Toggle Status button -->
                                        <button
                                            type="button"
                                            @click="openStatusModal({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                status: '{{ $user->status }}'
                                            })"
                                            class="p-1.5 rounded-lg {{ $user->isActive() ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition-colors"
                                            title="{{ $user->isActive() ? 'Désactiver le compte' : 'Activer le compte' }}"
                                        >
                                            @if ($user->isActive())
                                                <x-ui.icon name="alert-circle" class="w-4 h-4" />
                                            @else
                                                <x-ui.icon name="check-circle" class="w-4 h-4" />
                                            @endif
                                        </button>

                                        <!-- Reset Password button -->
                                        <button
                                            type="button"
                                            @click="openResetModal({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}'
                                            })"
                                            class="p-1.5 rounded-lg text-content-secondary hover:text-accent hover:bg-surface transition-colors"
                                            title="Réinitialiser le mot de passe"
                                        >
                                            <x-ui.icon name="settings" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8">
                                    <x-ui.empty-state
                                        icon="users"
                                        title="Aucun utilisateur trouvé"
                                        description="Aucun compte ne correspond à vos critères de recherche ou de filtre."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="px-5 py-4 border-t border-border bg-surface/30">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- SLIDE-OVER DRAWER : Création & Modification              -->
        <!-- ======================================================== -->
        <div
            x-show="drawerOpen"
            class="fixed inset-0 z-50 overflow-hidden"
            style="display: none;"
            aria-labelledby="slide-over-title"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div
                x-show="drawerOpen"
                x-transition:enter="ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity"
                @click="drawerOpen = false"
            ></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div
                    x-show="drawerOpen"
                    x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-md bg-white shadow-2xl border-l border-border flex flex-col"
                >
                    <!-- Drawer Header -->
                    <div class="px-6 py-5 bg-surface/50 border-b border-border flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <x-ui.icon name="user" class="w-5 h-5" />
                            </div>
                            <h3 id="slide-over-title" class="text-base font-bold text-content" x-text="drawerTitle"></h3>
                        </div>
                        <button
                            type="button"
                            @click="drawerOpen = false"
                            class="p-1 rounded-lg text-content-secondary hover:text-content hover:bg-surface"
                        >
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Drawer Form Content -->
                    <form :action="formAction" method="POST" class="flex-1 overflow-y-auto p-6 space-y-5">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Nom complet -->
                        <div>
                            <label for="drawer_name" class="block text-xs font-semibold text-content uppercase tracking-wider mb-1.5">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="drawer_name"
                                type="text"
                                name="name"
                                x-model="formData.name"
                                required
                                placeholder="ex. Sophie Martin"
                                class="w-full px-3.5 py-2.5 bg-white border border-border rounded-lg text-xs text-content placeholder-content-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                            />
                        </div>

                        <!-- Adresse e-mail -->
                        <div>
                            <label for="drawer_email" class="block text-xs font-semibold text-content uppercase tracking-wider mb-1.5">
                                Adresse e-mail professionnelle <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="drawer_email"
                                type="email"
                                name="email"
                                x-model="formData.email"
                                required
                                placeholder="ex. s.martin@entreprise.com"
                                class="w-full px-3.5 py-2.5 bg-white border border-border rounded-lg text-xs text-content placeholder-content-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                            />
                        </div>

                        <!-- Rôle -->
                        <div>
                            <label class="block text-xs font-semibold text-content uppercase tracking-wider mb-1.5">
                                Rôle dans l'application <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Option Admin -->
                                <label
                                    class="p-3 border rounded-xl cursor-pointer flex flex-col transition-all"
                                    :class="formData.role === '{{ \App\Models\User::ROLE_ADMIN }}' ? 'border-primary bg-primary/5 text-primary' : 'border-border hover:bg-surface text-content'"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-semibold text-xs">Administrateur</span>
                                        <input
                                            type="radio"
                                            name="role"
                                            value="{{ \App\Models\User::ROLE_ADMIN }}"
                                            x-model="formData.role"
                                            class="text-primary focus:ring-primary/20"
                                        />
                                    </div>
                                    <span class="text-[11px] text-content-secondary">Accès supervision & sécurité</span>
                                </label>

                                <!-- Option IT -->
                                <label
                                    class="p-3 border rounded-xl cursor-pointer flex flex-col transition-all"
                                    :class="formData.role === '{{ \App\Models\User::ROLE_RESPONSABLE_IT }}' ? 'border-accent bg-accent/5 text-accent' : 'border-border hover:bg-surface text-content'"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-semibold text-xs">Responsable IT</span>
                                        <input
                                            type="radio"
                                            name="role"
                                            value="{{ \App\Models\User::ROLE_RESPONSABLE_IT }}"
                                            x-model="formData.role"
                                            class="text-accent focus:ring-accent/20"
                                        />
                                    </div>
                                    <span class="text-[11px] text-content-secondary">Gestion matériel & stocks</span>
                                </label>
                            </div>
                        </div>

                        <!-- Statut -->
                        <div>
                            <label class="block text-xs font-semibold text-content uppercase tracking-wider mb-1.5">
                                Statut du compte <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="p-3 border rounded-xl cursor-pointer flex items-center justify-between transition-all"
                                    :class="formData.status === '{{ \App\Models\User::STATUS_ACTIVE }}' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-border hover:bg-surface text-content'"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-semibold text-xs">Actif</span>
                                    </div>
                                    <input
                                        type="radio"
                                        name="status"
                                        value="{{ \App\Models\User::STATUS_ACTIVE }}"
                                        x-model="formData.status"
                                        class="text-emerald-600 focus:ring-emerald-500/20"
                                    />
                                </label>

                                <label
                                    class="p-3 border rounded-xl cursor-pointer flex items-center justify-between transition-all"
                                    :class="formData.status === '{{ \App\Models\User::STATUS_INACTIVE }}' ? 'border-slate-400 bg-slate-100 text-slate-800' : 'border-border hover:bg-surface text-content'"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        <span class="font-semibold text-xs">Inactif</span>
                                    </div>
                                    <input
                                        type="radio"
                                        name="status"
                                        value="{{ \App\Models\User::STATUS_INACTIVE }}"
                                        x-model="formData.status"
                                        class="text-slate-600 focus:ring-slate-500/20"
                                    />
                                </label>
                            </div>
                        </div>

                        <!-- Mot de passe -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="drawer_password" class="block text-xs font-semibold text-content uppercase tracking-wider">
                                    <span x-text="isEditing ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe initial *'"></span>
                                </label>
                                <button
                                    type="button"
                                    @click="generatePassword()"
                                    class="text-[11px] font-medium text-primary hover:text-primary-light flex items-center gap-1"
                                >
                                    <x-ui.icon name="settings" class="w-3.5 h-3.5" />
                                    <span>Générer</span>
                                </button>
                            </div>
                            <input
                                id="drawer_password"
                                type="text"
                                name="password"
                                x-model="formData.password"
                                :required="!isEditing"
                                placeholder="Minimum 6 caractères"
                                class="w-full px-3.5 py-2.5 bg-white border border-border rounded-lg text-xs text-content placeholder-content-secondary/50 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                            />
                            <p class="text-[11px] text-content-secondary mt-1">
                                <span x-show="!isEditing">Communiquez ce mot de passe au collaborateur.</span>
                                <span x-show="isEditing">Laissez vide pour conserver le mot de passe actuel.</span>
                            </p>
                        </div>

                        <!-- Drawer Footer Buttons -->
                        <div class="pt-4 border-t border-border flex items-center justify-end gap-3">
                            <x-ui.button
                                type="button"
                                variant="outline"
                                size="md"
                                @click="drawerOpen = false"
                            >
                                Annuler
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="md"
                                icon="check-circle"
                            >
                                <span x-text="isEditing ? 'Enregistrer les modifications' : 'Créer l\'utilisateur'"></span>
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL : Confirmation de changement de Statut             -->
        <!-- ======================================================== -->
        <div
            x-show="statusModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="statusModalOpen = false"></div>

            <div
                x-show="statusModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-md mx-auto my-12 bg-white rounded-2xl border border-border shadow-2xl p-6 overflow-hidden"
            >
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                        :class="statusUser.status === 'actif' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'"
                    >
                        <x-ui.icon name="alert-circle" class="w-6 h-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-content">
                            <span x-text="statusUser.status === 'actif' ? 'Désactiver le compte ?' : 'Activer le compte ?'"></span>
                        </h3>
                        <p class="text-xs text-content-secondary mt-2 leading-relaxed">
                            <span x-show="statusUser.status === 'actif'">
                                En désactivant le compte de <strong class="text-content" x-text="statusUser.name"></strong>, l'utilisateur sera immédiatement déconnecté et ne pourra plus accéder à l'application. Ses historiques passés resteront préservés.
                            </span>
                            <span x-show="statusUser.status !== 'actif'">
                                En réactivant le compte de <strong class="text-content" x-text="statusUser.name"></strong>, l'utilisateur pourra à nouveau se connecter avec ses identifiants.
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" @click="statusModalOpen = false">
                        Annuler
                    </x-ui.button>
                    <form :action="'/admin/users/' + statusUser.id + '/toggle-status'" method="POST">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-lg text-white transition-colors"
                            :class="statusUser.status === 'actif' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                        >
                            <span x-text="statusUser.status === 'actif' ? 'Confirmer la désactivation' : 'Confirmer l\'activation'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL : Réinitialisation du mot de passe                 -->
        <!-- ======================================================== -->
        <div
            x-show="resetModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="resetModalOpen = false"></div>

            <div
                x-show="resetModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-md mx-auto my-12 bg-white rounded-2xl border border-border shadow-2xl p-6 overflow-hidden"
            >
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                        <x-ui.icon name="settings" class="w-6 h-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-content">Réinitialiser le mot de passe</h3>
                        <p class="text-xs text-content-secondary mt-1">
                            Compte de <strong class="text-content" x-text="resetUser.name"></strong>
                        </p>
                    </div>
                </div>

                <form :action="'/admin/users/' + resetUser.id + '/reset-password'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="reset_password_input" class="block text-xs font-semibold text-content uppercase tracking-wider">
                                Nouveau mot de passe
                            </label>
                            <button
                                type="button"
                                @click="generateResetPassword()"
                                class="text-[11px] font-medium text-primary hover:text-primary-light flex items-center gap-1"
                            >
                                <x-ui.icon name="settings" class="w-3.5 h-3.5" />
                                <span>Générer</span>
                            </button>
                        </div>
                        <input
                            id="reset_password_input"
                            type="text"
                            name="password"
                            x-model="resetPasswordValue"
                            required
                            placeholder="Minimum 6 caractères"
                            class="w-full px-3.5 py-2.5 bg-white border border-border rounded-lg text-xs text-content font-mono placeholder-content-secondary/50 focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-colors"
                        />
                        <p class="text-[11px] text-content-secondary mt-1">
                            Communiquez ce nouveau mot de passe directement à l'utilisateur.
                        </p>
                    </div>

                    <div class="pt-3 border-t border-border flex justify-end gap-3">
                        <x-ui.button variant="outline" size="sm" type="button" @click="resetModalOpen = false">
                            Annuler
                        </x-ui.button>
                        <x-ui.button variant="accent" size="sm" type="submit">
                            Mettre à jour le mot de passe
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.dashboard>
