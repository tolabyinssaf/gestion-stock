<x-layouts.dashboard title="Détail de l'activité" :breadcrumbs="['Administration' => route('admin.dashboard'), 'Journal d\'activités' => route('admin.activities.index'), 'Événement #'.$log->id => null]">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 p-6 bg-white rounded-2xl border border-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="activity" class="w-6 h-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl font-bold text-content tracking-tight">Détail de l'opération #{{ $log->id }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                            {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                        </span>
                    </div>
                    <p class="text-xs text-content-secondary mt-1">
                        Enregistré le {{ $log->created_at->format('d/m/Y à H:i:s') }}
                    </p>
                </div>
            </div>

            <a
                href="{{ route('admin.activities.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-content hover:bg-surface transition-colors"
            >
                <x-ui.icon name="arrow-left" class="w-4 h-4 text-content-secondary" />
                Retour au journal
            </a>
        </div>

        <!-- Meta Summary -->
        <div class="bg-white rounded-2xl border border-border shadow-xs p-6 sm:p-8 mb-8 space-y-6">
            <div class="border-b border-border pb-4 mb-4">
                <h3 class="text-sm font-bold text-content uppercase tracking-wider">Contexte d'exécution de l'opération</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-xs">
                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Auteur de l'action</span>
                    <span class="font-bold text-content text-sm">{{ $log->user_name ?? ($log->user->name ?? 'Système') }}</span>
                    @if ($log->user)
                        <div class="text-[11px] text-content-secondary mt-0.5">{{ $log->user->email }} ({{ $log->user->role_label }})</div>
                    @endif
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Module applicatif</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-surface border border-border font-semibold text-content text-xs uppercase">
                        {{ $log->module }}
                    </span>
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Adresse IP / Environnement</span>
                    <span class="font-mono text-xs text-content">{{ $log->ip_address ?? 'Non enregistrée' }}</span>
                </div>

                <div class="sm:col-span-2 md:col-span-3">
                    <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Description complète</span>
                    <div class="p-3.5 bg-surface rounded-xl border border-border text-xs font-medium text-content leading-relaxed">
                        {{ $log->description }}
                    </div>
                </div>

                @if ($log->entity_type || $log->entity_name)
                    <div class="sm:col-span-2 md:col-span-3">
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-1">Entité concernée</span>
                        <div class="text-xs text-content">
                            <strong>{{ $log->entity_type ?? 'Objet' }}</strong> : {{ $log->entity_name ?? 'ID #'.$log->entity_id }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Diff Viewer: Old Values vs New Values -->
        @if ($log->old_values || $log->new_values)
            <div class="bg-white rounded-2xl border border-border shadow-xs overflow-hidden mb-8">
                <div class="p-6 border-b border-border bg-surface/50">
                    <h3 class="text-xs font-bold text-content uppercase tracking-wider flex items-center gap-2">
                        <x-ui.icon name="file-diff" class="w-4 h-4 text-primary" />
                        Comparatif des valeurs autorisées (Avant / Après)
                    </h3>
                </div>

                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Anciennes valeurs -->
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-2 text-rose-700">
                            Anciennes valeurs (Avant)
                        </span>
                        @if ($log->old_values && count($log->old_values) > 0)
                            <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-200/60 font-mono text-xs text-rose-950 space-y-1.5 overflow-x-auto">
                                @foreach ($log->old_values as $key => $val)
                                    <div>
                                        <span class="font-semibold text-rose-900">{{ $key }}:</span>
                                        <span>{{ is_array($val) ? json_encode($val) : ($val ?? 'null') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 rounded-xl bg-surface border border-border text-xs text-content-secondary italic">
                                Aucune valeur antérieure enregistrée (création initiale ou non applicable).
                            </div>
                        @endif
                    </div>

                    <!-- Nouvelles valeurs -->
                    <div>
                        <span class="block text-[11px] font-bold text-content-secondary uppercase tracking-wider mb-2 text-emerald-700">
                            Nouvelles valeurs (Après)
                        </span>
                        @if ($log->new_values && count($log->new_values) > 0)
                            <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200/60 font-mono text-xs text-emerald-950 space-y-1.5 overflow-x-auto">
                                @foreach ($log->new_values as $key => $val)
                                    <div>
                                        <span class="font-semibold text-emerald-900">{{ $key }}:</span>
                                        <span>{{ is_array($val) ? json_encode($val) : ($val ?? 'null') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 rounded-xl bg-surface border border-border text-xs text-content-secondary italic">
                                Aucune nouvelle valeur renseignée.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center text-xs text-content-secondary pb-8">
            Ce journal d'audit est inaltérable et ne peut faire l'objet d'aucune modification manuelle.
        </div>
    </div>

</x-layouts.dashboard>
