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

// Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
$icons = [
    'chevron-down' => 'm6 9 6 6 6-6',
    'log-out'      => 'M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4|m16 17 5-5-5-5|M21 12H9',
];
$svg = function (string $name, string $class = 'w-4 h-4') use ($icons) {
    $paths = '';
    foreach (explode('|', $icons[$name] ?? '') as $d) {
        if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
};
@endphp

<header class="h-16 bg-white/95 backdrop-blur-md sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 transition-all duration-200 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
    <!-- LEFT: Sidebar Toggle (Trois tirets / Hamburger) -->
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <button
            type="button"
            @click="window.innerWidth < 1024 ? mobileSidebarOpen = !mobileSidebarOpen : toggleSidebar()"
            class="w-10 h-10 flex items-center justify-center rounded-full bg-surface text-content-secondary hover:text-[#0092FD] hover:bg-[#0092FD]/10 focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 transition-all duration-150 cursor-pointer"
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
                class="flex items-center gap-2.5 sm:gap-3 p-1 sm:pr-3.5 rounded-full bg-surface/70 hover:bg-surface focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 transition-colors cursor-pointer"
                aria-haspopup="true"
                :aria-expanded="open"
            >
                <!-- Avatar with Initials -->
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-white select-none flex-shrink-0 {{ $isAdmin ? 'bg-gradient-to-br from-[#0092FD] to-[#022D77] shadow-[0_4px_12px_rgba(0,146,253,0.35)]' : 'bg-gradient-to-br from-[#FE7A04] to-[#ff9a3c] shadow-[0_4px_12px_rgba(254,122,4,0.35)]' }}">
                    {{ $initials }}
                </div>

                <!-- Name & Role -->
                <div class="hidden sm:flex flex-col text-left">
                    <span class="text-xs font-bold text-content leading-tight truncate max-w-[140px]">
                        {{ $userName }}
                    </span>
                    <span class="text-[11px] font-semibold {{ $isAdmin ? 'text-[#0092FD]' : 'text-[#FE7A04]' }} leading-tight mt-0.5">
                        {{ $userRole }}
                    </span>
                </div>

                {!! $svg('chevron-down', 'hidden sm:block w-4 h-4 text-content-secondary transition-transform duration-200') !!}
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
                class="absolute right-0 mt-3 w-64 max-w-[calc(100vw-2rem)] bg-white rounded-3xl shadow-[0_12px_40px_rgba(2,45,119,0.18)] p-2 z-50"
                style="display: none;"
            >
                <!-- User Summary Header -->
                <div class="p-3.5 rounded-2xl bg-surface/70 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs text-white select-none flex-shrink-0 {{ $isAdmin ? 'bg-gradient-to-br from-[#0092FD] to-[#022D77]' : 'bg-gradient-to-br from-[#FE7A04] to-[#ff9a3c]' }}">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-content truncate">{{ $userName }}</p>
                        <p class="text-[11px] text-content-secondary truncate mt-0.5">{{ $userEmail }}</p>
                        <span class="inline-flex items-center mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $isAdmin ? 'bg-[#0092FD]/10 text-[#0092FD]' : 'bg-[#FE7A04]/10 text-[#FE7A04]' }}">
                            {{ $userRole }}
                        </span>
                    </div>
                </div>

                <!-- Logout -->
                <div class="mt-2 pt-2 border-t border-border/60">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="group w-full flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 group-hover:bg-rose-500 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                                {!! $svg('log-out', 'w-4 h-4') !!}
                            </span>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>