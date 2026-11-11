@props([
    'title' => 'Tableau de bord',
    'breadcrumbs' => [],
])

@php
$user = Auth::user();
$userName = $user ? $user->name : 'Utilisateur';
$userEmail = $user ? $user->email : '';
$userRole = $user ? $user->role_label : '';
$isAdmin = $user && $user->isAdmin();

// Generate initials (e.g., "AD" for Administrateur or "RI" for Responsable IT)
$words = preg_split('/\s+/', trim($userName));
$initials = '';
foreach (array_slice($words, 0, 2) as $w) {
    if (!empty($w)) {
        $initials .= mb_strtoupper(mb_substr($w, 0, 1));
    }
}
if (empty($initials)) {
    $initials = 'IT';
}
@endphp

<header class="h-16 bg-white/95 backdrop-blur-md border-b border-border sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 transition-all duration-200 shadow-xs">
    <!-- LEFT: Sidebar Toggle + Title + Breadcrumbs -->
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <!-- Mobile Sidebar Toggle -->
        <button
            type="button"
            @click="mobileSidebarOpen = !mobileSidebarOpen"
            class="lg:hidden p-2 rounded-lg text-content-secondary hover:text-content hover:bg-surface focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors"
            aria-label="Ouvrir le menu latéral"
        >
            <x-ui.icon name="menu" class="w-5 h-5" />
        </button>

        <!-- Desktop Sidebar Collapse Toggle -->
        <button
            type="button"
            @click="sidebarCollapsed = !sidebarCollapsed"
            class="hidden lg:flex p-2 rounded-lg text-content-secondary hover:text-content hover:bg-surface focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors"
            :title="sidebarCollapsed ? 'Agrandir la barre latérale' : 'Réduire la barre latérale'"
            aria-label="Basculer la barre latérale"
        >
            <svg
                class="w-5 h-5 transition-transform duration-200"
                :class="sidebarCollapsed ? 'rotate-180' : ''"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <rect width="18" height="18" x="3" y="3" rx="2" />
                <path d="M9 3v18" />
                <path d="m14 15-3-3 3-3" />
            </svg>
        </button>

        <!-- Page Title & Breadcrumbs -->
        <div class="flex flex-col min-w-0">
            <h1 class="text-base sm:text-lg font-bold text-content tracking-tight truncate">
                {{ $title }}
            </h1>

            @if (!empty($breadcrumbs))
                <div class="hidden sm:block -mt-0.5">
                    <x-breadcrumb :items="$breadcrumbs" />
                </div>
            @endif
        </div>
    </div>

    <!-- RIGHT: Search Slot + User Menu -->
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Search bar placeholder (distinctly marked as planned) -->
        <div class="hidden md:flex items-center">
            <div class="relative group">
                <button
                    type="button"
                    @click="$dispatch('open-unavailable', { title: 'Recherche globale' })"
                    class="flex items-center gap-2 pl-3 pr-2.5 py-1.5 bg-surface hover:bg-slate-100 border border-border/80 rounded-lg text-xs text-content-secondary transition-colors"
                    title="Recherche globale (bientôt disponible)"
                >
                    <x-ui.icon name="search" class="w-4 h-4 text-content-secondary/70" />
                    <span>Rechercher...</span>
                    <kbd class="ml-2 px-1.5 py-0.5 text-[10px] bg-white border border-border rounded font-mono text-content-secondary/70">Ctrl K</kbd>
                </button>
            </div>
        </div>

        <!-- Vertical Separator -->
        <div class="hidden sm:block h-6 w-px bg-border"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
            <button
                type="button"
                @click="open = !open"
                class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-surface focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors"
                aria-haspopup="true"
                :aria-expanded="open"
            >
                <!-- Avatar with Initials -->
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs text-white shadow-xs select-none {{ $isAdmin ? 'bg-primary' : 'bg-accent' }}">
                    {{ $initials }}
                </div>

                <!-- Name & Role -->
                <div class="hidden sm:flex flex-col text-left">
                    <span class="text-xs font-semibold text-content leading-tight truncate max-w-[140px]">
                        {{ $userName }}
                    </span>
                    <span class="text-[11px] font-medium {{ $isAdmin ? 'text-primary' : 'text-accent' }} leading-tight mt-0.5">
                        {{ $userRole }}
                    </span>
                </div>

                <x-ui.icon name="chevron-down" class="hidden sm:block w-4 h-4 text-content-secondary transition-transform duration-200" ::class="open ? 'rotate-180' : ''" />
            </button>

            <!-- Dropdown Menu -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl border border-border shadow-lg py-1.5 z-50 overflow-hidden"
                style="display: none;"
            >
                <!-- User Summary Header -->
                <div class="px-4 py-2.5 border-b border-border/80 bg-surface/40">
                    <p class="text-xs font-semibold text-content truncate">{{ $userName }}</p>
                    <p class="text-[11px] text-content-secondary truncate mt-0.5">{{ $userEmail }}</p>
                    <div class="mt-1.5">
                        <x-ui.badge :variant="$isAdmin ? 'admin' : 'it'" size="sm">
                            {{ $userRole }}
                        </x-ui.badge>
                    </div>
                </div>

                <!-- Links -->
                <div class="py-1">
                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-content hover:bg-surface hover:text-primary transition-colors"
                    >
                        <x-ui.icon name="user" class="w-4 h-4 text-content-secondary" />
                        <span>Mon profil</span>
                    </a>
                </div>

                <!-- Logout -->
                <div class="border-t border-border/80 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors text-left"
                        >
                            <x-ui.icon name="log-out" class="w-4 h-4 text-red-600" />
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
