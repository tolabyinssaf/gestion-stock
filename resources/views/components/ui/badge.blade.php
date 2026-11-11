@props([
    'variant' => 'neutral',
    'size' => 'md',
    'dot' => false,
])

@php
$sizeClasses = match ($size) {
    'sm' => 'px-2 py-0.5 text-[11px]',
    'lg' => 'px-3 py-1 text-sm',
    default => 'px-2.5 py-1 text-xs',
};

$variantClasses = match ($variant) {
    'primary', 'admin' => 'bg-primary/10 text-primary border border-primary/20',
    'accent', 'it' => 'bg-accent/10 text-accent border border-accent/20',
    'success', 'disponible' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    'info', 'en_utilisation' => 'bg-blue-50 text-blue-700 border border-blue-200',
    'warning', 'stock_faible' => 'bg-amber-50 text-amber-700 border border-amber-200',
    'danger', 'en_panne' => 'bg-rose-50 text-rose-700 border border-rose-200',
    'neutral', 'hors_service' => 'bg-slate-100 text-slate-700 border border-slate-200',
    'soon' => 'bg-purple-50 text-purple-700 border border-purple-200',
    default => 'bg-slate-100 text-slate-700 border border-slate-200',
};

$dotClasses = match ($variant) {
    'primary', 'admin' => 'bg-primary',
    'accent', 'it' => 'bg-accent',
    'success', 'disponible' => 'bg-emerald-500',
    'info', 'en_utilisation' => 'bg-blue-500',
    'warning', 'stock_faible' => 'bg-amber-500',
    'danger', 'en_panne' => 'bg-rose-500',
    'neutral', 'hors_service' => 'bg-slate-400',
    'soon' => 'bg-purple-500',
    default => 'bg-slate-400',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded-full {$sizeClasses} {$variantClasses}"]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
    @endif
    <span>{{ $slot }}</span>
</span>
