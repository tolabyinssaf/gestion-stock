@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed select-none';

$sizeClasses = match ($size) {
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'lg' => 'px-5 py-3 text-base gap-2.5',
    default => 'px-4 py-2 text-sm gap-2',
};

$variantClasses = match ($variant) {
    'primary' => 'bg-primary text-white hover:bg-primary-light active:bg-primary-dark focus:ring-primary/30 shadow-xs',
    'secondary' => 'bg-primary-light text-white hover:bg-primary active:bg-primary-dark focus:ring-primary-light/30 shadow-xs',
    'accent' => 'bg-accent text-white hover:bg-accent-light active:opacity-90 focus:ring-accent/30 shadow-xs',
    'outline' => 'bg-white text-content border border-border hover:bg-surface active:bg-slate-100 focus:ring-primary/20',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 focus:ring-red-500/30 shadow-xs',
    'ghost' => 'text-content-secondary hover:text-content hover:bg-slate-100 active:bg-slate-200 focus:ring-slate-300',
    default => 'bg-primary text-white hover:bg-primary-light focus:ring-primary/30',
};

$classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon && $iconPosition === 'left')
            <x-ui.icon :name="$icon" class="w-4 h-4" />
        @endif
        <span>{{ $slot }}</span>
        @if ($icon && $iconPosition === 'right')
            <x-ui.icon :name="$icon" class="w-4 h-4" />
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif ($icon && $iconPosition === 'left')
            <x-ui.icon :name="$icon" class="w-4 h-4" />
        @endif

        <span>{{ $slot }}</span>

        @if ($icon && $iconPosition === 'right' && !$loading)
            <x-ui.icon :name="$icon" class="w-4 h-4" />
        @endif
    </button>
@endif
