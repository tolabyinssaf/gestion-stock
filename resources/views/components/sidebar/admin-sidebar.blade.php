@props([])

<aside
    class="fixed inset-y-0 left-0 z-40 bg-white border-r border-border flex flex-col transition-all duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:flex-shrink-0"
    :class="{
        'w-64': !sidebarCollapsed,
        'w-20': sidebarCollapsed,
        'translate-x-0': mobileSidebarOpen,
        '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
    }"
>
    <!-- Brand Header -->
    <div class="h-16 flex items-center px-4 border-b border-border/80 flex-shrink-0" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center flex-shrink-0 text-white shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/><path d="M12 12h.01"/>
                </svg>
            </div>
            <div class="flex flex-col min-w-0 transition-opacity duration-200" x-show="!sidebarCollapsed">
                <span class="text-sm font-bold text-content tracking-tight truncate">Stock Matériel</span>
                <span class="text-[11px] font-semibold text-primary uppercase tracking-wider">Administration</span>
            </div>
        </a>

        <!-- Close button on mobile -->
        <button
            type="button"
            @click="mobileSidebarOpen = false"
            class="lg:hidden p-1.5 rounded-lg text-content-secondary hover:text-content hover:bg-surface"
        >
            <x-ui.icon name="x" class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation Menu (Scrollable) -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6">

        <!-- SECTION: PRINCIPAL -->
        <div>
            <p
                x-show="!sidebarCollapsed"
                class="px-3 text-[11px] font-semibold text-content-secondary/80 uppercase tracking-wider mb-2"
            >
                Principal
            </p>
            <div class="space-y-1">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 relative {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-content hover:bg-surface hover:text-primary' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Tableau de bord' : ''"
                >
                    <x-ui.icon name="layout-dashboard" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-content-secondary group-hover:text-primary' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate">Tableau de bord</span>
                    @if (request()->routeIs('admin.dashboard'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-primary rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION: ADMINISTRATION -->
        <div>
            <p
                x-show="!sidebarCollapsed"
                class="px-3 text-[11px] font-semibold text-content-secondary/80 uppercase tracking-wider mb-2"
            >
                Administration
            </p>
            <div class="space-y-1">
                <!-- Utilisateurs -->
                <a
                    href="{{ route('admin.users.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 relative {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary' : 'text-content hover:bg-surface hover:text-primary' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Utilisateurs' : ''"
                >
                    <x-ui.icon name="users" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-primary' : 'text-content-secondary group-hover:text-primary' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Utilisateurs</span>
                    @if (request()->routeIs('admin.users.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-primary rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION: SUPERVISION -->
        <div>
            <p
                x-show="!sidebarCollapsed"
                class="px-3 text-[11px] font-semibold text-content-secondary/80 uppercase tracking-wider mb-2"
            >
                Supervision
            </p>
            <div class="space-y-1">
                <!-- Journal des activités -->
                <a
                    href="{{ route('admin.activities.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 relative {{ request()->routeIs('admin.activities.*') ? 'bg-primary/10 text-primary' : 'text-content hover:bg-surface hover:text-primary' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Journal des activités' : ''"
                >
                    <x-ui.icon name="activity" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.activities.*') ? 'text-primary' : 'text-content-secondary group-hover:text-primary' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Journal des activités</span>
                    @if (request()->routeIs('admin.activities.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-primary rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION: CONFIGURATION -->
        <div>
            <p
                x-show="!sidebarCollapsed"
                class="px-3 text-[11px] font-semibold text-content-secondary/80 uppercase tracking-wider mb-2"
            >
                Configuration
            </p>
            <div class="space-y-1">
                <!-- Paramètres -->
                <a
                    href="{{ route('admin.settings.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 relative {{ request()->routeIs('admin.settings.*') ? 'bg-primary/10 text-primary' : 'text-content hover:bg-surface hover:text-primary' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Paramètres' : ''"
                >
                    <x-ui.icon name="settings" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-primary' : 'text-content-secondary group-hover:text-primary' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Paramètres</span>
                    @if (request()->routeIs('admin.settings.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-primary rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION: COMPTE -->
        <div class="pt-2 border-t border-border/80">
            <p
                x-show="!sidebarCollapsed"
                class="px-3 text-[11px] font-semibold text-content-secondary/80 uppercase tracking-wider mb-2"
            >
                Compte
            </p>
            <div class="space-y-1">
                <!-- Mon profil -->
                <a
                    href="{{ route('profile.edit') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('profile.edit') ? 'bg-primary/10 text-primary font-semibold' : 'text-content hover:bg-surface hover:text-primary' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Mon profil' : ''"
                >
                    <x-ui.icon name="user" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('profile.edit') ? 'text-primary' : 'text-content-secondary group-hover:text-primary' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate">Mon profil</span>
                </a>

                <!-- Déconnexion -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium text-red-600 hover:bg-red-50 transition-all duration-150 text-left"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                        :title="sidebarCollapsed ? 'Déconnexion' : ''"
                    >
                        <x-ui.icon name="log-out" class="w-5 h-5 flex-shrink-0 text-red-500 group-hover:text-red-600" />
                        <span x-show="!sidebarCollapsed" class="truncate">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Bottom Role Indicator Footer -->
    <div class="p-3 border-t border-border/80 bg-surface/40 flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 font-bold text-xs">
            AD
        </div>
        <div class="flex-1 min-w-0" x-show="!sidebarCollapsed">
            <p class="text-xs font-semibold text-content truncate">{{ Auth::user()->name }}</p>
            <p class="text-[11px] text-content-secondary truncate">Administrateur</p>
        </div>
    </div>
</aside>
