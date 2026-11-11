@props([
    'title' => 'Tableau de bord',
    'breadcrumbs' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' — IT Manager Pro' : 'IT Manager Pro' }}</title>

    <!-- Google / Bunny Font : Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="h-full font-sans antialiased bg-surface text-content flex flex-col"
    x-data="{
        sidebarCollapsed: localStorage.getItem('it_sidebar_collapsed') === 'true',
        mobileSidebarOpen: false,
        unavailableModal: false,
        unavailableTitle: '',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('it_sidebar_collapsed', this.sidebarCollapsed);
        }
    }"
    @open-unavailable.window="unavailableModal = true; unavailableTitle = $event.detail.title || 'Fonctionnalité'"
    @keydown.escape.window="mobileSidebarOpen = false; unavailableModal = false"
>
    <div class="min-h-screen flex flex-row">

        <!-- Mobile Backdrop Overlay -->
        <div
            x-show="mobileSidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileSidebarOpen = false"
            class="fixed inset-0 bg-content/50 backdrop-blur-xs z-30 lg:hidden"
            style="display: none;"
            aria-hidden="true"
        ></div>

        <!-- Role-Specific Sidebar Component -->
        @if (Auth::user() && Auth::user()->isAdmin())
            <x-sidebar.admin-sidebar />
        @else
            <x-sidebar.it-sidebar />
        @endif

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">

            <!-- Top Header -->
            <x-header :title="$title" :breadcrumbs="$breadcrumbs" />

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="py-4 px-6 border-t border-border/80 bg-white text-center text-xs text-content-secondary">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>&copy; {{ date('Y') }} IT Manager Pro &mdash; Gestion professionnelle du parc informatique.</p>
                    <div class="flex items-center gap-3 text-[11px]">
                        <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Système opérationnel
                        </span>
                        <span class="text-border">&bull;</span>
                        <span>Version 1.0</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Global "Module en cours de développement" Informative Modal -->
    <div
        x-show="unavailableModal"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        style="display: none;"
        role="dialog"
        aria-modal="true"
    >
        <div class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity" @click="unavailableModal = false"></div>

        <div
            x-show="unavailableModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-md mx-auto my-12 bg-white rounded-2xl border border-border shadow-2xl p-6 overflow-hidden"
        >
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="info" class="w-6 h-6" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-content truncate" x-text="unavailableTitle"></h3>
                    </div>
                    <span class="inline-block mt-1">
                        <x-ui.badge variant="soon" size="sm">Étape suivante du projet</x-ui.badge>
                    </span>
                    <p class="text-xs text-content-secondary mt-2.5 leading-relaxed">
                        Ce module fait partie de l'architecture générale d'IT Manager Pro et sera développé lors des prochaines étapes selon la feuille de route.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.button variant="primary" size="sm" @click="unavailableModal = false">
                    Compris
                </x-ui.button>
            </div>
        </div>
    </div>
</body>
</html>
