@php
    // Icônes SVG intégrées (style Lucide) : s'affichent toujours, sans dépendre de x-ui.icon.
    $icons = [
        'plus'           => 'M5 12h14|M12 5v14',
        'x'              => 'M18 6 6 18|m6 6 12 12',
        'mail'           => 'm22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7|M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        'file-text'      => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z|M14 2v4a2 2 0 0 0 2 2h4|M10 9H8|M16 13H8|M16 17H8',
        'edit-3'         => 'M12 20h9|M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z',
        'clock'          => 'M12 6v6l4 2|M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0z',
        'search'         => 'm21 21-4.34-4.34|M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16z',
        'filter'         => 'M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z',
        'check-circle'   => 'M21.801 10A10 10 0 1 1 17 3.335|m9 11 3 3L22 4',
        'alert-triangle' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3|M12 9v4|M12 17h.01',
        'alert-circle'   => 'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0z|M12 8v4|M12 16h.01',
        'eye'            => 'M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0|M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
        'edit'           => 'M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z|m15 5 4 4',
        'copy'           => 'M8 8h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2z|M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2',
        'archive'        => 'M2 3h20v5H2z|M4 8v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8|M10 12h4',
        'arrow-up-right' => 'M7 7h10v10|M7 17 17 7',
    ];
    $svg = function (string $name, string $class = 'w-5 h-5') use ($icons) {
        $paths = '';
        foreach (explode('|', $icons[$name] ?? '') as $d) {
            if ($d !== '') { $paths .= '<path d="' . $d . '"/>'; }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . $paths . '</svg>';
    };
    $hasFilters = request()->hasAny(['search', 'priorite', 'statut_preparation', 'show_archived']);
@endphp

<x-layouts.dashboard title="Demandes de matériel" :breadcrumbs="['Demandes de matériel' => null]">

    <div>
        <!-- Messages flash -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                {!! $svg('check-circle', 'w-5 h-5 text-emerald-600 flex-shrink-0') !!}
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 bg-rose-50 rounded-2xl flex items-center gap-3 text-rose-800 text-sm">
                {!! $svg('alert-triangle', 'w-5 h-5 text-rose-600 flex-shrink-0') !!}
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Barre d'actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-xs sm:text-sm text-content-secondary">Préparez vos demandes de matériel.</p>
            <a
                href="{{ route('it.demandes.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold shadow-[0_6px_16px_rgba(0,146,253,0.35)] transition-colors"
            >
                {!! $svg('plus', 'w-4 h-4') !!}
                Nouvelle demande
            </a>
        </div>

        <!-- 3 cartes statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
            <!-- Total -->
            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-[#0092FD]/10 text-[#0092FD] flex items-center justify-center flex-shrink-0">
                            {!! $svg('file-text') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">Total des demandes</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $totalDemandes }}</p>
                <p class="text-xs text-content-secondary mt-1">Toutes demandes confondues</p>
            </div>

            <!-- Brouillons -->
            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-[#022D77]/10 text-[#022D77] flex items-center justify-center flex-shrink-0">
                            {!! $svg('edit-3') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">Brouillons</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $brouillonsCount }}</p>
                <p class="text-xs text-content-secondary mt-1">En cours de préparation</p>
            </div>

            <!-- À traiter -->
            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-[#FE7A04]/10 text-[#FE7A04] flex items-center justify-center flex-shrink-0">
                            {!! $svg('clock') !!}
                        </div>
                        <p class="text-sm font-semibold text-content truncate">Demandes à traiter</p>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-surface text-content-secondary flex items-center justify-center flex-shrink-0">
                        {!! $svg('arrow-up-right', 'w-4 h-4') !!}
                    </span>
                </div>
                <p class="mt-4 text-2xl sm:text-3xl font-extrabold text-content tracking-tight">{{ $demandesATraiterCount }}</p>
                <p class="text-xs text-content-secondary mt-1">À envoyer ou à suivre</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 mb-6 shadow-[0_2px_14px_rgba(2,45,119,0.06)]">
            <form method="GET" action="{{ route('it.demandes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Recherche -->
                <div class="sm:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-secondary">
                        {!! $svg('search', 'w-4 h-4') !!}
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher référence, matériel, motif..."
                        class="block w-full pl-10 pr-4 py-2.5 bg-surface/60 border border-transparent rounded-full text-xs text-content placeholder-content-secondary/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                </div>

                <!-- Priorité -->
                <div>
                    <select
                        name="priorite"
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Toutes les priorités</option>
                        <option value="normale" {{ request('priorite') === 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="importante" {{ request('priorite') === 'importante' ? 'selected' : '' }}>Importante</option>
                        <option value="urgente" {{ request('priorite') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <!-- Statut -->
                <div>
                    <select
                        name="statut_preparation"
                        class="block w-full py-2.5 px-4 bg-surface/60 border border-transparent rounded-full text-xs text-content focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0092FD]/25 focus:border-[#0092FD] transition-all"
                    >
                        <option value="">Tous les statuts</option>
                        <option value="brouillon" {{ request('statut_preparation') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="prete" {{ request('statut_preparation') === 'prete' ? 'selected' : '' }}>Préparée</option>
                        <option value="ouverture_gmail" {{ request('statut_preparation') === 'ouverture_gmail' ? 'selected' : '' }}>Ouverte dans Gmail</option>
                        <option value="envoyee" {{ request('statut_preparation') === 'envoyee' ? 'selected' : '' }}>Déclarée envoyée</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold rounded-full transition-colors cursor-pointer"
                    >
                        {!! $svg('filter', 'w-4 h-4') !!}
                        Filtrer
                    </button>
                    @if ($hasFilters)
                        <a
                            href="{{ route('it.demandes.index') }}"
                            class="inline-flex items-center justify-center p-2.5 bg-surface hover:bg-slate-100 text-content-secondary hover:text-content rounded-full transition-colors flex-shrink-0"
                            title="Réinitialiser"
                        >
                            {!! $svg('x', 'w-4 h-4') !!}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste des demandes -->
        <div class="mb-10 bg-white rounded-3xl shadow-[0_2px_14px_rgba(2,45,119,0.06)] overflow-hidden">
            @if ($demandes->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-surface mx-auto flex items-center justify-center text-content-secondary mb-4">
                        {!! $svg('mail', 'w-8 h-8') !!}
                    </div>
                    <h3 class="text-base font-bold text-content">Aucune demande trouvée</h3>
                    <p class="text-xs text-content-secondary mt-1 max-w-sm mx-auto">
                        Vous n'avez préparé aucune demande de matériel pour le moment.
                    </p>
                    <div class="mt-5">
                        <a
                            href="{{ route('it.demandes.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0092FD] hover:bg-[#022D77] text-white text-xs font-semibold rounded-full transition-colors"
                        >
                            {!! $svg('plus', 'w-4 h-4') !!}
                            Nouvelle demande
                        </a>
                    </div>
                </div>
            @else
                <!-- Mobile / tablette : cartes -->
                <div class="lg:hidden p-4 space-y-3">
                    @foreach ($demandes as $demande)
                        <div class="p-4 rounded-2xl bg-surface/60">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('it.demandes.show', $demande) }}" class="font-mono text-xs font-bold text-[#0092FD] hover:underline">
                                        {{ $demande->reference }}
                                    </a>
                                    <p class="text-sm font-bold text-content break-words mt-1">
                                        {{ $demande->produit_designation ?? ($demande->produit->nom ?? 'N/A') }}
                                    </p>
                                    <p class="text-[11px] text-content-secondary mt-0.5 font-mono">
                                        Réf : {{ $demande->produit_reference ?? $demande->produit->reference }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                    @if ($demande->priorite === 'urgente')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">
                                            {!! $svg('alert-triangle', 'w-3 h-3') !!}
                                            Urgente
                                        </span>
                                    @elseif ($demande->priorite === 'importante')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">
                                            {!! $svg('alert-circle', 'w-3 h-3') !!}
                                            Importante
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-white text-content-secondary">
                                            Normale
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-border/60 grid grid-cols-2 gap-3 text-[11px]">
                                <div>
                                    <p class="text-[10px] text-content-secondary mb-1">Statut</p>
                                    @if ($demande->statut_preparation === 'envoyee')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Envoyée (déclaratif)
                                        </span>
                                    @elseif ($demande->statut_preparation === 'ouverture_gmail')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-[#0092FD]/10 text-[#0092FD]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#0092FD]"></span>
                                            Ouverte dans Gmail
                                        </span>
                                    @elseif ($demande->statut_preparation === 'prete')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-[#FE7A04]/10 text-[#FE7A04]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#FE7A04]"></span>
                                            Préparée
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Brouillon
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-content-secondary mb-1">Quantité</p>
                                    <p class="text-xs font-bold text-content">{{ $demande->quantite }} unité(s)</p>
                                    @if ($demande->prix_estime)
                                        <p class="text-[11px] text-content-secondary mt-0.5">
                                            ~ {{ number_format($demande->prix_estime * $demande->quantite, 2, ',', ' ') }} €
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-border/60 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-content-secondary">{{ $demande->created_at->format('d/m/Y') }}</span>

                                <div class="flex items-center justify-end flex-wrap gap-1">
                                    <a
                                        href="{{ route('it.demandes.show', $demande) }}"
                                        class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-[#0092FD] hover:bg-white rounded-full transition-colors"
                                        title="Voir les détails"
                                    >
                                        {!! $svg('eye', 'w-4 h-4') !!}
                                    </a>

                                    @if ($demande->statut_preparation === 'brouillon')
                                        <a
                                            href="{{ route('it.demandes.edit', $demande) }}"
                                            class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-[#FE7A04] hover:bg-white rounded-full transition-colors"
                                            title="Modifier le brouillon"
                                        >
                                            {!! $svg('edit', 'w-4 h-4') !!}
                                        </a>
                                    @endif

                                    @if ($demande->email_destinataire)
                                        <a
                                            href="{{ $demande->gmail_compose_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-[#0092FD] hover:bg-white rounded-full transition-colors"
                                            title="Ouvrir dans Gmail"
                                        >
                                            {!! $svg('mail', 'w-4 h-4') !!}
                                        </a>
                                    @endif

                                    @if (in_array($demande->statut_preparation, ['prete', 'ouverture_gmail']))
                                        <form method="POST" action="{{ route('it.demandes.mark-sent', $demande) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-emerald-600 hover:bg-emerald-50 rounded-full transition-colors cursor-pointer"
                                                title="Marquer comme envoyée (suivi déclaratif)"
                                                onclick="return confirm('Confirmez-vous avoir envoyé cet e-mail depuis votre messagerie Gmail ?')"
                                            >
                                                {!! $svg('check-circle', 'w-4 h-4') !!}
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('it.demandes.duplicate', $demande) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-content hover:bg-white rounded-full transition-colors cursor-pointer"
                                            title="Dupliquer la demande"
                                        >
                                            {!! $svg('copy', 'w-4 h-4') !!}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('it.demandes.archive', $demande) }}" class="inline" onsubmit="return confirm('Voulez-vous vraiment archiver cette demande ?')">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-rose-600 hover:bg-rose-50 rounded-full transition-colors cursor-pointer"
                                            title="Archiver la demande"
                                        >
                                            {!! $svg('archive', 'w-4 h-4') !!}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PC : tableau -->
                <div class="hidden lg:block overflow-x-auto p-4">
                    <table class="w-full text-left text-xs border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-surface text-[11px] font-semibold text-content">
                                <th class="py-3 px-5 rounded-l-xl">Référence</th>
                                <th class="py-3 px-4">Matériel</th>
                                <th class="py-3 px-4">Quantité demandée</th>
                                <th class="py-3 px-4">Priorité</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-5 text-right rounded-r-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($demandes as $demande)
                                <tr class="hover:bg-surface/50 transition-colors [&>td]:border-b [&>td]:border-border/50 last:[&>td]:border-b-0">
                                    <!-- Référence -->
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <a href="{{ route('it.demandes.show', $demande) }}" class="font-mono text-xs font-bold text-[#0092FD] hover:underline">
                                            {{ $demande->reference }}
                                        </a>
                                    </td>

                                    <!-- Matériel -->
                                    <td class="py-3.5 px-4 min-w-[180px]">
                                        <div class="text-xs font-bold text-content">
                                            {{ $demande->produit_designation ?? ($demande->produit->nom ?? 'N/A') }}
                                        </div>
                                        <div class="text-[11px] text-content-secondary mt-0.5 font-mono">
                                            Réf : {{ $demande->produit_reference ?? $demande->produit->reference }}
                                        </div>
                                    </td>

                                    <!-- Quantité demandée -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs font-semibold text-content">
                                        {{ $demande->quantite }} unité(s)
                                        @if ($demande->prix_estime)
                                            <div class="text-[11px] text-content-secondary font-normal mt-0.5">
                                                ~ {{ number_format($demande->prix_estime * $demande->quantite, 2, ',', ' ') }} €
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Priorité -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($demande->priorite === 'urgente')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                                {!! $svg('alert-triangle', 'w-3 h-3') !!}
                                                Urgente
                                            </span>
                                        @elseif ($demande->priorite === 'importante')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                                {!! $svg('alert-circle', 'w-3 h-3') !!}
                                                Importante
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-surface text-content-secondary">
                                                Normale
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Statut -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($demande->statut_preparation === 'envoyee')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Envoyée (déclaratif)
                                            </span>
                                        @elseif ($demande->statut_preparation === 'ouverture_gmail')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-[#0092FD]/10 text-[#0092FD]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#0092FD]"></span>
                                                Ouverte dans Gmail
                                            </span>
                                        @elseif ($demande->statut_preparation === 'prete')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FE7A04]/10 text-[#FE7A04]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#FE7A04]"></span>
                                                Préparée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Brouillon
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Date -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-content-secondary">
                                        {{ $demande->created_at->format('d/m/Y') }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-5 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <!-- Voir détails -->
                                            <a
                                                href="{{ route('it.demandes.show', $demande) }}"
                                                class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-[#0092FD] hover:bg-surface rounded-full transition-colors"
                                                title="Voir les détails"
                                            >
                                                {!! $svg('eye', 'w-4 h-4') !!}
                                            </a>

                                            <!-- Modifier si brouillon -->
                                            @if ($demande->statut_preparation === 'brouillon')
                                                <a
                                                    href="{{ route('it.demandes.edit', $demande) }}"
                                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-[#FE7A04] hover:bg-[#FE7A04]/10 rounded-full transition-colors"
                                                    title="Modifier le brouillon"
                                                >
                                                    {!! $svg('edit', 'w-4 h-4') !!}
                                                </a>
                                            @endif

                                            <!-- Ouvrir dans Gmail -->
                                            @if ($demande->email_destinataire)
                                                <a
                                                    href="{{ $demande->gmail_compose_url }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-[#0092FD] hover:bg-surface rounded-full transition-colors"
                                                    title="Ouvrir dans Gmail"
                                                >
                                                    {!! $svg('mail', 'w-4 h-4') !!}
                                                </a>
                                            @endif

                                            <!-- Déclarer envoyée -->
                                            @if (in_array($demande->statut_preparation, ['prete', 'ouverture_gmail']))
                                                <form method="POST" action="{{ route('it.demandes.mark-sent', $demande) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-emerald-600 hover:bg-emerald-50 rounded-full transition-colors cursor-pointer"
                                                        title="Marquer comme envoyée (suivi déclaratif)"
                                                        onclick="return confirm('Confirmez-vous avoir envoyé cet e-mail depuis votre messagerie Gmail ?')"
                                                    >
                                                        {!! $svg('check-circle', 'w-4 h-4') !!}
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Dupliquer -->
                                            <form method="POST" action="{{ route('it.demandes.duplicate', $demande) }}" class="inline">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-content hover:bg-surface rounded-full transition-colors cursor-pointer"
                                                    title="Dupliquer la demande"
                                                >
                                                    {!! $svg('copy', 'w-4 h-4') !!}
                                                </button>
                                            </form>

                                            <!-- Archiver -->
                                            <form method="POST" action="{{ route('it.demandes.archive', $demande) }}" class="inline" onsubmit="return confirm('Voulez-vous vraiment archiver cette demande ?')">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="w-8 h-8 inline-flex items-center justify-center text-content-secondary hover:text-rose-600 hover:bg-rose-50 rounded-full transition-colors cursor-pointer"
                                                    title="Archiver la demande"
                                                >
                                                    {!! $svg('archive', 'w-4 h-4') !!}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($demandes->hasPages())
                    <div class="p-4 border-t border-border/50">
                        {{ $demandes->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-layouts.dashboard>