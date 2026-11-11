@props([
    'name',
    'title' => null,
    'maxWidth' => 'md', // sm, md, lg, xl
])

@php
$maxWidthClasses = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    default => 'sm:max-w-md',
};
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: none;"
>
    <!-- Overlay backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-content/50 backdrop-blur-xs transition-opacity"
        @click="show = false"
    ></div>

    <!-- Modal Box -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full mx-auto my-8 bg-white rounded-2xl border border-border shadow-xl overflow-hidden {{ $maxWidthClasses }}"
    >
        @if ($title)
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-semibold text-content">{{ $title }}</h3>
                <button
                    type="button"
                    @click="show = false"
                    class="p-1 rounded-lg text-content-secondary hover:text-content hover:bg-surface transition-colors"
                >
                    <x-ui.icon name="x" class="w-5 h-5" />
                </button>
            </div>
        @endif

        <div class="p-6">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="px-6 py-3.5 bg-surface/60 border-t border-border flex items-center justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
