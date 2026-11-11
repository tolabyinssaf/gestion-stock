@props([
    'icon' => 'boxes',
    'title' => 'Aucune donnée disponible',
    'description' => 'Les informations s\'afficheront ici dès qu\'elles seront créées.',
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 bg-white rounded-xl border border-dashed border-border']) }}>
    <div class="w-14 h-14 rounded-2xl bg-surface border border-border flex items-center justify-center text-content-secondary mb-4">
        <x-ui.icon :name="$icon" class="w-7 h-7" />
    </div>

    <h4 class="text-base font-semibold text-content">
        {{ $title }}
    </h4>

    <p class="text-sm text-content-secondary max-w-sm mt-1.5 leading-relaxed">
        {{ $description }}
    </p>

    @if ($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endif
</div>
