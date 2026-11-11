@props([
    'title',
    'description',
    'icon' => 'plus',
    'href' => null,
    'color' => 'primary',
    'available' => true,
])

@php
$colorStyles = match ($color) {
    'accent' => 'group-hover:bg-accent group-hover:text-white text-accent bg-accent/10',
    'secondary' => 'group-hover:bg-primary-light group-hover:text-white text-primary-light bg-primary-light/10',
    default => 'group-hover:bg-primary group-hover:text-white text-primary bg-primary/10',
};
@endphp

@if ($available && $href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block p-4 bg-white hover:bg-surface border border-border hover:border-primary/40 rounded-xl shadow-xs hover:shadow-sm transition-all duration-200 text-left']) }}>
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-200 {{ $colorStyles }}">
                <x-ui.icon :name="$icon" class="w-5 h-5" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm font-semibold text-content group-hover:text-primary transition-colors duration-150 truncate">
                        {{ $title }}
                    </h4>
                    <x-ui.icon name="chevron-right" class="w-4 h-4 text-content-secondary group-hover:text-primary group-hover:translate-x-0.5 transition-all duration-150 flex-shrink-0" />
                </div>
                <p class="text-xs text-content-secondary mt-1 line-clamp-2 leading-relaxed">
                    {{ $description }}
                </p>
            </div>
        </div>
    </a>
@else
    <button
        type="button"
        @click="$dispatch('open-unavailable', { title: '{{ addslashes($title) }}' })"
        {{ $attributes->merge(['class' => 'group w-full block p-4 bg-white hover:bg-slate-50 border border-border hover:border-slate-300 rounded-xl shadow-xs transition-all duration-200 text-left relative']) }}
    >
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-200 {{ $colorStyles }}">
                <x-ui.icon :name="$icon" class="w-5 h-5" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm font-semibold text-content group-hover:text-primary transition-colors duration-150 truncate">
                        {{ $title }}
                    </h4>
                    <x-ui.badge variant="soon" size="sm">Bientôt disponible</x-ui.badge>
                </div>
                <p class="text-xs text-content-secondary mt-1 line-clamp-2 leading-relaxed">
                    {{ $description }}
                </p>
            </div>
        </div>
    </button>
@endif
