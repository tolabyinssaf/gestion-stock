@props([
    'title',
    'value' => null,
    'icon' => 'activity',
    'color' => 'primary', // primary, secondary, accent, emerald, amber, rose, slate
    'subtext' => null,
    'badge' => null,
    'badgeVariant' => 'neutral',
    'isPending' => false,
])

@php
$colorStyles = match ($color) {
    'primary' => [
        'bg' => 'bg-primary/10',
        'text' => 'text-primary',
        'border' => 'border-primary/20',
    ],
    'secondary' => [
        'bg' => 'bg-primary-light/10',
        'text' => 'text-primary-light',
        'border' => 'border-primary-light/20',
    ],
    'accent' => [
        'bg' => 'bg-accent/10',
        'text' => 'text-accent',
        'border' => 'border-accent/20',
    ],
    'emerald', 'success' => [
        'bg' => 'bg-emerald-50',
        'text' => 'text-emerald-600',
        'border' => 'border-emerald-200',
    ],
    'amber', 'warning' => [
        'bg' => 'bg-amber-50',
        'text' => 'text-amber-600',
        'border' => 'border-amber-200',
    ],
    'rose', 'danger' => [
        'bg' => 'bg-rose-50',
        'text' => 'text-rose-600',
        'border' => 'border-rose-200',
    ],
    default => [
        'bg' => 'bg-slate-100',
        'text' => 'text-slate-600',
        'border' => 'border-slate-200',
    ],
};
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-border p-5 shadow-xs hover:shadow-sm transition-shadow duration-200 relative overflow-hidden']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-content-secondary uppercase tracking-wider truncate mb-1">
                {{ $title }}
            </p>

            <div class="flex items-baseline gap-2">
                @if ($isPending)
                    <span class="text-2xl font-bold text-content-secondary/60 tracking-tight">&mdash;</span>
                @else
                    <span class="text-2xl lg:text-3xl font-bold text-content tracking-tight">
                        {{ $value ?? '0' }}
                    </span>
                @endif

                @if ($badge)
                    <x-ui.badge :variant="$badgeVariant" size="sm">
                        {{ $badge }}
                    </x-ui.badge>
                @endif
            </div>

            @if ($subtext)
                <p class="text-xs text-content-secondary mt-1.5 flex items-center gap-1 truncate">
                    {{ $subtext }}
                </p>
            @endif
        </div>

        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $colorStyles['bg'] }} {{ $colorStyles['text'] }}">
            <x-ui.icon :name="$icon" class="w-6 h-6" />
        </div>
    </div>

    @if ($isPending)
        <div class="mt-3 pt-2.5 border-t border-border/60 flex items-center justify-between text-[11px] text-content-secondary">
            <span class="inline-flex items-center gap-1 text-slate-400">
                <x-ui.icon name="info" class="w-3.5 h-3.5" />
                Données en attente du module
            </span>
            <span class="text-[10px] font-medium text-primary">À venir</span>
        </div>
    @endif
</div>
