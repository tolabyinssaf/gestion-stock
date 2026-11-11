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
        <a href="{{ route('it.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center flex-shrink-0 text-white shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>
                </svg>
            </div>
            <div class="flex flex-col min-w-0 transition-opacity duration-200" x-show="!sidebarCollapsed">
                <span class="text-sm font-bold text-content tracking-tight truncate">Stock Matériel</span>
                <span class="text-[11px] font-semibold text-accent uppercase tracking-wider">Gestionnaire de stock</span>
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

        <!-- SECTION 1: TABLEAU DE BORD -->
        <div>
            <div class="space-y-1">
                <a
                    href="{{ route('it.dashboard') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 relative {{ request()->routeIs('it.dashboard') ? 'bg-accent/10 text-accent font-bold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Tableau de bord' : ''"
                >
                    <x-ui.icon name="layout-dashboard" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('it.dashboard') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Tableau de bord</span>
                    @if (request()->routeIs('it.dashboard'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-accent rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION 2: GESTION DU MATÉRIEL -->
        <div>
            <div class="space-y-1">
                <a
                    href="{{ route('it.products.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 relative {{ request()->routeIs('it.products.*') || request()->routeIs('it.categories.*') || request()->routeIs('it.equipements.*') ? 'bg-accent/10 text-accent font-semibold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Gestion du matériel' : ''"
                >
                    <x-ui.icon name="monitor" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('it.products.*') || request()->routeIs('it.categories.*') || request()->routeIs('it.equipements.*') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Gestion du matériel</span>
                    @if (request()->routeIs('it.products.*') || request()->routeIs('it.categories.*') || request()->routeIs('it.equipements.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-accent rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION 3: GESTION DU STOCK -->
        <div>
            <div class="space-y-1">
                <a
                    href="{{ route('it.stocks.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 relative {{ request()->routeIs('it.stocks.*') || request()->routeIs('it.emplacements.*') ? 'bg-accent/10 text-accent font-semibold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Gestion du stock' : ''"
                >
                    <x-ui.icon name="boxes" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('it.stocks.*') || request()->routeIs('it.emplacements.*') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Gestion du stock</span>
                    @if (request()->routeIs('it.stocks.*') || request()->routeIs('it.emplacements.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-accent rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION 4: HISTORIQUE -->
        <div>
            <div class="space-y-1">
                <a
                    href="{{ route('it.mouvements.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 relative {{ request()->routeIs('it.mouvements.*') ? 'bg-accent/10 text-accent font-semibold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Historique' : ''"
                >
                    <x-ui.icon name="history" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('it.mouvements.*') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Historique</span>
                    @if (request()->routeIs('it.mouvements.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-accent rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION 5: DEMANDES DE MATÉRIEL -->
        <div>
            <div class="space-y-1">
                <a
                    href="{{ route('it.demandes.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 relative {{ request()->routeIs('it.demandes.*') ? 'bg-accent/10 text-accent font-semibold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Demandes de matériel' : ''"
                >
                    <x-ui.icon name="mail" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('it.demandes.*') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Demandes de matériel</span>
                    @if (request()->routeIs('it.demandes.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-accent rounded-r-full" x-show="!sidebarCollapsed"></span>
                    @endif
                </a>
            </div>
        </div>

        <!-- SECTION 6: RAPPORTS ET EXPORTS -->
        <div>
            <div class="space-y-1">
                <a
                    href="{{ route('it.reports.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 relative {{ request()->routeIs('it.reports.*') ? 'bg-accent/10 text-accent font-semibold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Rapports et exports' : ''"
                >
                    <x-ui.icon name="bar-chart-3" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('it.reports.*') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
                    <span x-show="!sidebarCollapsed" class="truncate flex-1">Rapports & Exports</span>
                    @if (request()->routeIs('it.reports.*'))
                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-accent rounded-r-full" x-show="!sidebarCollapsed"></span>
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
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('profile.edit') ? 'bg-accent/10 text-accent font-semibold' : 'text-content hover:bg-surface hover:text-accent' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                    :title="sidebarCollapsed ? 'Mon profil' : ''"
                >
                    <x-ui.icon name="user" class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('profile.edit') ? 'text-accent' : 'text-content-secondary group-hover:text-accent' }}" />
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
        <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center flex-shrink-0 font-bold text-xs">
            GS
        </div>
        <div class="flex-1 min-w-0" x-show="!sidebarCollapsed">
            <p class="text-xs font-semibold text-content truncate">{{ Auth::user()->name }}</p>
            <p class="text-[11px] text-content-secondary truncate">Gestionnaire de stock</p>
        </div>
    </div>
</aside>
