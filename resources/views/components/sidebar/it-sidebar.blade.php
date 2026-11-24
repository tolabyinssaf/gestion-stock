@props([])

@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'x'                => 'M18 6 6 18|m6 6 12 12',
        'layout-dashboard' => 'M3 3h7v9H3z|M14 3h7v5h-7z|M14 12h7v9h-7z|M3 16h7v5H3z',
        'monitor'          => 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z|M8 21h8|M12 17v4',
        'boxes'            => 'M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z|m7 16.5-4.74-2.85|m7 16.5 5-3|M7 16.5v5.17|M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z|m17 16.5-5-3|m17 16.5 4.74-2.85|M17 16.5v5.17|M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z|M12 8 7.26 5.15|m12 8 4.74-2.85|M12 13.5V8',
        'history'          => 'M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8|M3 3v5h5|M12 7v5l4 2',
        'mail'             => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'bar-chart-3'      => 'M3 3v16a2 2 0 0 0 2 2h16|M18 17V9|M13 17V5|M8 17v-3',
        'log-out'          => 'M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4|m16 17 5-5-5-5|M21 12H9',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };

    // Entrées du menu : route, libellé, icône et motifs de routes qui rendent l'entrée active.
    $menu = [
        ['route' => 'it.dashboard',       'label' => 'Tableau de bord',       'title' => 'Tableau de bord',       'icon' => 'layout-dashboard', 'active' => ['it.dashboard']],
        ['route' => 'it.products.index',  'label' => 'Gestion du matériel',   'title' => 'Gestion du matériel',   'icon' => 'monitor',          'active' => ['it.products.*', 'it.categories.*', 'it.equipements.*']],
        ['route' => 'it.stocks.index',    'label' => 'Gestion du stock',      'title' => 'Gestion du stock',      'icon' => 'boxes',            'active' => ['it.stocks.*']],
        ['route' => 'it.mouvements.index','label' => 'Historique',            'title' => 'Historique',            'icon' => 'history',          'active' => ['it.mouvements.*']],
        ['route' => 'it.demandes.index',  'label' => 'Demandes de matériel',  'title' => 'Demandes de matériel',  'icon' => 'mail',             'active' => ['it.demandes.*']],
        ['route' => 'it.reports.index',   'label' => 'Rapports & Exports',    'title' => 'Rapports et exports',   'icon' => 'bar-chart-3',      'active' => ['it.reports.*']],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 bg-white shadow-[4px_0_24px_rgba(2,45,119,0.08)] lg:shadow-[2px_0_20px_rgba(2,45,119,0.06)] flex flex-col transition-all duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:flex-shrink-0 w-64"
    :class="{
        'w-64': !sidebarCollapsed,
        'w-20': sidebarCollapsed,
        'translate-x-0': mobileSidebarOpen,
        '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
    }"
>
    <!-- Brand Header -->
    <div class="h-20 relative flex items-center justify-center px-4 flex-shrink-0 overflow-hidden">
        <a href="{{ route('it.dashboard') }}" class="flex items-center justify-center w-full h-full overflow-hidden">
            <img
                src="/images/logo.png"
                alt="VAR du Lassureur"
                width="160"
                height="40"
                class="sidebar-brand-logo object-contain transition-all duration-150 mx-auto"
                :class="sidebarCollapsed ? '!h-7 !max-w-[48px]' : 'h-10 max-w-[160px]'"
                style="max-height: 40px; max-width: 160px;"
            />
        </a>

        <!-- Close button on mobile -->
        <button
            type="button"
            @click="mobileSidebarOpen = false"
            class="lg:hidden absolute right-3 top-3 w-9 h-9 flex items-center justify-center rounded-full bg-surface text-content-secondary hover:text-content hover:bg-slate-100 transition-colors cursor-pointer"
        >
            {!! $svg('x', 'w-5 h-5') !!}
        </button>
    </div>

    <!-- Séparateur dégradé -->
    <div class="mx-5 h-px bg-gradient-to-r from-transparent via-[#0092FD]/30 to-transparent flex-shrink-0"></div>

    <!-- Navigation Menu (Scrollable) -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1.5">

        <p x-show="!sidebarCollapsed" class="px-4 pb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-content-secondary/60">Navigation</p>

        @foreach ($menu as $item)
            @php
                $isActive = request()->routeIs(...$item['active']);
            @endphp
            <a
                href="{{ route($item['route']) }}"
                class="group relative flex items-center gap-3 pl-2 pr-4 py-2 rounded-full text-xs transition-all duration-200 {{ $isActive ? 'bg-[#0092FD] text-white font-bold shadow-[0_8px_20px_rgba(0,146,253,0.35)]' : 'font-medium text-content hover:bg-[#0092FD]/8 hover:text-[#0092FD]' }}"
                :class="sidebarCollapsed ? 'justify-center !px-2' : ''"
                :title="sidebarCollapsed ? '{{ $item['title'] }}' : ''"
            >
                <span class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-200 {{ $isActive ? 'bg-white/20 text-white' : 'bg-surface text-content-secondary group-hover:bg-[#0092FD] group-hover:text-white group-hover:shadow-[0_4px_12px_rgba(0,146,253,0.35)]' }}">
                    {!! $svg($item['icon'], 'w-[18px] h-[18px]') !!}
                </span>
                <span x-show="!sidebarCollapsed" class="truncate flex-1">{{ $item['label'] }}</span>
                @if ($isActive)
                    <span x-show="!sidebarCollapsed" class="w-1.5 h-1.5 rounded-full bg-[#FE7A04] flex-shrink-0"></span>
                @endif
            </a>
        @endforeach

        <!-- SECTION : COMPTE -->
        <div class="pt-3 mt-3 border-t border-border/60">
            <!-- Déconnexion -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="group w-full flex items-center gap-3 pl-2 pr-4 py-2 rounded-full text-xs font-medium text-rose-600 hover:bg-rose-50 transition-all duration-200 text-left cursor-pointer"
                    :class="sidebarCollapsed ? 'justify-center !px-2' : ''"
                    :title="sidebarCollapsed ? 'Déconnexion' : ''"
                >
                    <span class="w-9 h-9 rounded-full bg-rose-50 text-rose-500 group-hover:bg-rose-500 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-colors">
                        {!! $svg('log-out', 'w-[18px] h-[18px]') !!}
                    </span>
                    <span x-show="!sidebarCollapsed" class="truncate">Déconnexion</span>
                </button>
            </form>
        </div>

    </div>

    <!-- Bottom Role Indicator Footer -->
    <div class="p-3 flex-shrink-0">
        <div class="p-2.5 rounded-2xl bg-surface/80 flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#0092FD] to-[#022D77] text-white flex items-center justify-center flex-shrink-0 font-bold text-xs shadow-[0_4px_12px_rgba(0,146,253,0.35)]">
                GS
            </div>
            <div class="flex-1 min-w-0" x-show="!sidebarCollapsed">
                <p class="text-xs font-semibold text-content truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-content-secondary truncate">Gestionnaire de stock</p>
            </div>
        </div>
    </div>
</aside>