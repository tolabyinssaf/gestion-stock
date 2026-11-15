@props([
    'title' => 'Tableau de bord',
    'breadcrumbs' => [],
])

@php
$user = Auth::user();
$userName = $user ? $user->name : 'Utilisateur';
if ($userName === 'Responsable IT') {
    $userName = 'Gestionnaire de stock';
}
$userEmail = $user ? $user->email : '';
$userRole = $user ? $user->role_label : '';
$isAdmin = $user && $user->isAdmin();

// Generate initials (e.g., "AD" for Administrateur or "GS" for Gestionnaire de stock)
$words = preg_split('/\s+/', trim($userName));
$initials = '';
foreach (array_slice($words, 0, 2) as $w) {
    if (!empty($w)) {
        $initials .= mb_strtoupper(mb_substr($w, 0, 1));
    }
}
if (empty($initials)) {
    $initials = 'GS';
}
@endphp

<header class="h-16 bg-white/95 backdrop-blur-md border-b border-border sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 transition-all duration-200 shadow-xs">
    <!-- LEFT: Sidebar Toggle (Trois tirets / Hamburger) -->
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <button
            type="button"
            @click="window.innerWidth < 1024 ? mobileSidebarOpen = !mobileSidebarOpen : toggleSidebar()"
            class="p-2 rounded-xl text-content-secondary hover:text-content hover:bg-surface focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-150 cursor-pointer"
            title="Ouvrir / Fermer le menu"
            aria-label="Ouvrir / Fermer le menu latéral"
        >
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="6" y2="6"/>
                <line x1="4" x2="20" y1="12" y2="12"/>
                <line x1="4" x2="20" y1="18" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- RIGHT: User Menu -->
    <div class="flex items-center gap-3 sm:gap-4">
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
