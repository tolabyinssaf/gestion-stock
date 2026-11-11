<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — Stock Matériel</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-surface" x-data="loginForm()">
    <div class="min-h-screen flex">

        <!-- LEFT PANEL - Presentation (hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden" style="background: linear-gradient(135deg, #014392 0%, #0161B9 50%, #013070 100%);">
            <!-- Subtle pattern overlay -->
            <div class="absolute inset-0 opacity-[0.07]">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-16 w-full">
                <!-- Logo & App Name -->
                <div class="mb-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-14 h-14 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <div>
                            <h1 class="text-white text-2xl font-bold tracking-tight">Stock Matériel</h1>
                            <p class="text-blue-200 text-sm font-medium">Gestion du stock de matériel</p>
                        </div>
                    </div>

                    <h2 class="text-white text-3xl xl:text-4xl font-bold leading-tight mb-4">
                        Votre stock de matériel,<br>
                        <span class="text-blue-200">simple et sous contrôle.</span>
                    </h2>
                    <p class="text-blue-200/80 text-base leading-relaxed max-w-md">
                        Suivez vos produits, ajustez les quantités en temps réel et anticipez les réapprovisionnements en toute simplicité.
                    </p>
                </div>

                <!-- Feature Cards -->
                <div class="space-y-4">
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transition-all duration-300 hover:bg-white/15">
                        <div class="w-10 h-10 bg-white/15 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">Catalogue du matériel</h3>
                            <p class="text-blue-200/70 text-xs mt-0.5">Organisez vos produits et catégories</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transition-all duration-300 hover:bg-white/15">
                        <div class="w-10 h-10 bg-white/15 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">Gestion des quantités en direct</h3>
                            <p class="text-blue-200/70 text-xs mt-0.5">Visualisez les stocks disponibles et les alertes</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transition-all duration-300 hover:bg-white/15">
                        <div class="w-10 h-10 bg-white/15 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">Historique des mouvements</h3>
                            <p class="text-blue-200/70 text-xs mt-0.5">Entrées, sorties, transferts et corrections</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL - Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col">
            <!-- Mobile header -->
            <div class="lg:hidden flex items-center gap-3 p-6 pb-0">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
                <span class="text-lg font-bold text-content">Stock Matériel</span>
            </div>

            <div class="flex-1 flex items-center justify-center px-6 sm:px-12 lg:px-16 xl:px-20">
                <div class="w-full max-w-md">
                    <!-- Welcome Header -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-content mb-2">Connexion</h2>
                        <p class="text-content-secondary text-sm">Connectez-vous pour gérer votre stock.</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" @submit="handleSubmit">
                        @csrf

                        <!-- Email -->
                        <div class="mb-5">
                            <label for="email" class="block text-sm font-semibold text-content mb-2">Adresse email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="nom@entreprise.com"
                                    class="block w-full pl-11 pr-4 py-3 bg-white border border-border rounded-lg text-sm text-content placeholder-content-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200"
                                />
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-5">
                            <label for="password" class="block text-sm font-semibold text-content mb-2">Mot de passe</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Votre mot de passe"
                                    class="block w-full pl-11 pr-12 py-3 bg-white border border-border rounded-lg text-sm text-content placeholder-content-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-content-secondary hover:text-content transition-colors duration-200"
                                    tabindex="-1"
                                >
                                    <template x-if="!showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/></svg>
                                    </template>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember me & Forgot password -->
                        <div class="flex items-center justify-between mb-6">
                            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="w-4 h-4 rounded border-border text-primary focus:ring-primary/20 cursor-pointer"
                                />
                                <span class="text-sm text-content-secondary">Se souvenir de moi</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:text-primary-light transition-colors duration-200">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg text-white text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed"
                            :class="isLoading ? 'bg-primary/70' : 'bg-primary hover:bg-primary-light active:bg-primary-dark'"
                        >
                            <template x-if="!isLoading">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                                    Se connecter
                                </span>
                            </template>
                            <template x-if="isLoading">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Connexion en cours...
                                </span>
                            </template>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-8 pt-6 border-t border-border">
                        <p class="text-center text-xs text-content-secondary">
                            &copy; {{ date('Y') }} Stock Matériel &mdash; Gestion du stock
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                showPassword: false,
                isLoading: false,
                handleSubmit() {
                    this.isLoading = true;
                }
            }
        }
    </script>
</body>
</html>
