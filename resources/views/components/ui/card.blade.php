@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'footer' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-border shadow-xs overflow-hidden transition-all duration-200']) }}>
    @if ($title || $subtitle || $action || isset($header))
        <div class="px-5 py-4 border-b border-border/80 flex items-center justify-between gap-4">
            @if (isset($header))
                {{ $header }}
            @else
                <div>
                    @if ($title)
                        <h3 class="text-base font-semibold text-content leading-tight">{{ $title }}</h3>
                    @endif
                    @if ($subtitle)
                        <p class="text-xs text-content-secondary mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
                @if ($action)
                    <div class="flex items-center gap-2">
                        {{ $action }}
                    </div>
                @endif
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>

    @if ($footer || isset($footerSlot))
        <div class="px-5 py-3 bg-surface/60 border-t border-border/80 text-xs text-content-secondary">
            {{ $footer ?? $footerSlot }}
        </div>
    @endif
</div>
