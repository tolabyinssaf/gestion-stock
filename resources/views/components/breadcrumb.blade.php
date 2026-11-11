@props([
    'items' => [], // array of ['label' => '...', 'url' => '...'] or key => value
])

@php
$formattedItems = [];
foreach ($items as $key => $value) {
    if (is_array($value)) {
        $formattedItems[] = $value;
    } elseif (is_string($key)) {
        $formattedItems[] = ['label' => $key, 'url' => $value];
    } else {
        $formattedItems[] = ['label' => $value, 'url' => null];
    }
}
@endphp

@if (count($formattedItems) > 0)
    <nav aria-label="Fil d'Ariane" {{ $attributes->merge(['class' => 'flex items-center text-xs text-content-secondary']) }}>
        <ol class="flex items-center space-x-1.5 flex-wrap">
            @foreach ($formattedItems as $index => $item)
                @if ($index > 0)
                    <li class="flex items-center text-content-secondary/60">
                        <x-ui.icon name="chevron-right" class="w-3.5 h-3.5" />
                    </li>
                @endif

                <li class="flex items-center">
                    @if (!empty($item['url']) && $index < count($formattedItems) - 1)
                        <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors duration-150">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-content {{ $index === count($formattedItems) - 1 ? 'text-primary' : '' }}">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
